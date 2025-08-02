<?php
/**
 * Loan/Payment Calculator
 * Used Car Purchase Website - Interactive Financial Calculator
 */

// Include session management and configuration
require_once '../php/session.php';
require_once '../php/navigation.php';
require_once '../php/config.php';
require_once '../php/seo.php';

// Get car information if car_id is provided
$selected_car = null;
$car_id = intval($_GET['car_id'] ?? 0);

if ($car_id > 0) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT id, name, make, model, year, price, image FROM cars WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $selected_car = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
    } catch (Exception $e) {
        error_log("Error fetching car for calculator: " . $e->getMessage());
    }
}

// Handle calculator session saving (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_calculation') {
    header('Content-Type: application/json');
    
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Security token validation failed.']);
        exit();
    }
    
    // Validate input data
    $car_price = filter_var($_POST['car_price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $down_payment = filter_var($_POST['down_payment'] ?? 0, FILTER_VALIDATE_FLOAT);
    $loan_term = filter_var($_POST['loan_term'] ?? 0, FILTER_VALIDATE_INT);
    $interest_rate = filter_var($_POST['interest_rate'] ?? 0, FILTER_VALIDATE_FLOAT);
    $monthly_payment = filter_var($_POST['monthly_payment'] ?? 0, FILTER_VALIDATE_FLOAT);
    $total_payment = filter_var($_POST['total_payment'] ?? 0, FILTER_VALIDATE_FLOAT);
    $total_interest = filter_var($_POST['total_interest'] ?? 0, FILTER_VALIDATE_FLOAT);
    $car_id = filter_var($_POST['car_id'] ?? null, FILTER_VALIDATE_INT);
    
    if ($car_price && $loan_term && $interest_rate && $monthly_payment) {
        try {
            $connection = getDatabaseConnection();
            $query = "INSERT INTO calculator_sessions (user_id, car_id, car_price, down_payment, loan_term, interest_rate, monthly_payment, total_payment, total_interest, session_ip) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $connection->prepare($query);
            $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            $stmt->bind_param("iiddidiids", 
                $user_id, $car_id, $car_price, $down_payment, $loan_term, 
                $interest_rate, $monthly_payment, $total_payment, $total_interest, $ip_address
            );
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Calculation saved successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save calculation.']);
            }
            
            $stmt->close();
            $connection->close();
        } catch (Exception $e) {
            error_log("Error saving calculator session: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid calculation data.']);
    }
    exit();
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<?php
// Generate SEO meta tags
$seo_config = getSEOConfig('calculator.php', [
    'og_image' => '/images/calculator-social.png', // Calculator-specific image
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php echo generateSEOMetaTags($seo_config); ?>
    <link rel="stylesheet" href="../css/theme-default.css" id="theme-link">
    
    <style>
        /* Calculator-specific styles */
        .calculator-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .calculator-hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .calculator-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .calculator-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .calculator-form {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }
        
        .calculator-results {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .form-group input[type="number"],
        .form-group input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
        }
        
        .input-with-prefix {
            position: relative;
        }
        
        .input-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-weight: 600;
            pointer-events: none;
        }
        
        .input-with-prefix input {
            padding-left: 2rem;
        }
        
        .input-suffix {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-weight: 600;
            pointer-events: none;
        }
        
        .input-with-suffix input {
            padding-right: 2rem;
        }
        
        .calculate-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .calculate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .results-section {
            margin-bottom: 2rem;
        }
        
        .results-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--light-gray);
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
        }
        
        .result-label {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .result-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .primary-result {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 1.5rem;
        }
        
        .primary-result .result-label,
        .primary-result .result-value {
            color: white;
        }
        
        .primary-result .result-value {
            font-size: 2rem;
        }
        
        .selected-car {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }
        
        .selected-car h4 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .car-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .car-image {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .car-details {
            flex: 1;
        }
        
        .car-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }
        
        .car-price {
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .payment-breakdown {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
            border: 1px solid var(--border-color);
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .breakdown-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .action-btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--secondary-color);
            border: 2px solid var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-primary {
            background: var(--secondary-color);
            color: white;
            border: 2px solid var(--secondary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .calculation-tips {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            margin-bottom: 3rem;
        }
        
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .tip-item {
            padding: 1rem;
            background: var(--light-gray);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .tip-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .tip-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .tip-description {
            color: var(--text-dark);
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .calculator-hero {
                padding: 3rem 1rem;
            }
            
            .calculator-hero h2 {
                font-size: 2rem;
            }
            
            .calculator-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .tips-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="theme-switcher">
            <select id="theme-select" class="theme-select">
                <option value="default">Default Theme</option>
                <option value="dark">Dark Theme</option>
                <option value="light">Light Theme</option>
            </select>
        </div>
        <div class="logo-text" style="font-size: 2rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;" role="banner" aria-label="AutoDeals Logo">🚗 AutoDeals</div>
        <h1>Used Car Purchase Website</h1>
        <?php echo generateHeaderGreeting(); ?>
    </header>

    <?php echo generateNavigation('calculator.php'); ?>

    <main>
        <section class="calculator-hero">
            <h2>💰 Loan Payment Calculator</h2>
            <p>Calculate your estimated monthly payments and total loan costs. Get instant results to help you make informed financing decisions.</p>
        </section>

        <?php if ($selected_car): ?>
        <div class="selected-car">
            <h4>🎯 Selected Vehicle</h4>
            <div class="car-info">
                <img src="<?php echo sanitizeOutput($selected_car['image']); ?>" alt="<?php echo sanitizeOutput($selected_car['name']); ?>" class="car-image">
                <div class="car-details">
                    <div class="car-name"><?php echo sanitizeOutput($selected_car['name']); ?></div>
                    <div class="car-price"><?php echo formatPrice($selected_car['price']); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="calculator-container">
            <div class="calculator-form">
                <form id="loanCalculatorForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
                    
                    <div class="form-section">
                        <h3>💵 Loan Details</h3>
                        
                        <div class="form-group">
                            <label for="car_price">Vehicle Price</label>
                            <div class="input-with-prefix">
                                <span class="input-prefix">$</span>
                                <input type="number" id="car_price" name="car_price" 
                                       value="<?php echo $selected_car ? $selected_car['price'] : '25000'; ?>" 
                                       min="1000" max="200000" step="100" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="down_payment">Down Payment</label>
                            <div class="input-with-prefix">
                                <span class="input-prefix">$</span>
                                <input type="number" id="down_payment" name="down_payment" 
                                       value="<?php echo $selected_car ? round($selected_car['price'] * 0.2) : '5000'; ?>" 
                                       min="0" step="100" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="loan_term">Loan Term</label>
                                <div class="input-with-suffix">
                                    <input type="number" id="loan_term" name="loan_term" 
                                           value="5" min="1" max="10" step="1" required>
                                    <span class="input-suffix">years</span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="interest_rate">Interest Rate</label>
                                <div class="input-with-suffix">
                                    <input type="number" id="interest_rate" name="interest_rate" 
                                           value="4.5" min="0.1" max="20" step="0.1" required>
                                    <span class="input-suffix">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="calculateBtn" class="calculate-btn">
                        🧮 Calculate Payment
                    </button>
                </form>
            </div>
            
            <div class="calculator-results">
                <div class="results-section">
                    <h3>📊 Payment Results</h3>
                    
                    <div class="result-item primary-result" id="monthlyPaymentResult">
                        <div class="result-label">Monthly Payment</div>
                        <div class="result-value" id="monthlyPaymentValue">$0.00</div>
                    </div>
                    
                    <div class="result-item" id="totalPaymentResult">
                        <div class="result-label">Total Payment</div>
                        <div class="result-value" id="totalPaymentValue">$0.00</div>
                    </div>
                    
                    <div class="result-item" id="totalInterestResult">
                        <div class="result-label">Total Interest</div>
                        <div class="result-value" id="totalInterestValue">$0.00</div>
                    </div>
                </div>
                
                <div class="payment-breakdown" id="paymentBreakdown" style="display: none;">
                    <h4>Payment Breakdown</h4>
                    <div class="breakdown-item">
                        <span>Principal Amount:</span>
                        <span id="principalAmount">$0.00</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Down Payment:</span>
                        <span id="downPaymentDisplay">$0.00</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Loan Amount:</span>
                        <span id="loanAmount">$0.00</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Interest Rate:</span>
                        <span id="interestRateDisplay">0.0%</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Loan Term:</span>
                        <span id="loanTermDisplay">0 years</span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button type="button" id="saveCalculationBtn" class="action-btn btn-secondary">
                        💾 Save Calculation
                    </button>
                    <?php if ($selected_car): ?>
                        <a href="inquiry.php?car_id=<?php echo $car_id; ?>" class="action-btn btn-primary">
                            📧 Inquire About Car
                        </a>
                    <?php else: ?>
                        <a href="cars.php" class="action-btn btn-primary">
                            🔍 Browse Cars
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="calculation-tips">
            <h3 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">💡 Financing Tips</h3>
            <div class="tips-grid">
                <div class="tip-item">
                    <div class="tip-icon">📈</div>
                    <div class="tip-title">Down Payment</div>
                    <div class="tip-description">A larger down payment reduces your monthly payment and total interest paid over the loan term.</div>
                </div>
                
                <div class="tip-item">
                    <div class="tip-icon">⏰</div>
                    <div class="tip-title">Loan Term</div>
                    <div class="tip-description">Shorter loan terms mean higher monthly payments but less total interest paid.</div>
                </div>
                
                <div class="tip-item">
                    <div class="tip-icon">🎯</div>
                    <div class="tip-title">Interest Rate</div>
                    <div class="tip-description">Shop around for the best interest rates. Your credit score affects the rate you'll qualify for.</div>
                </div>
                
                <div class="tip-item">
                    <div class="tip-icon">💡</div>
                    <div class="tip-title">Budget Planning</div>
                    <div class="tip-description">Keep your total monthly vehicle expenses (payment, insurance, maintenance) under 20% of income.</div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="../js/main.js"></script>
    <script src="../js/theme-switcher.js"></script>
    
    <script>
        // Loan Calculator JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loanCalculatorForm');
            const calculateBtn = document.getElementById('calculateBtn');
            const saveBtn = document.getElementById('saveCalculationBtn');
            
            // Auto-calculate on input change
            const inputs = form.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('input', calculatePayment);
            });
            
            // Calculate button click
            calculateBtn.addEventListener('click', calculatePayment);
            
            // Save calculation
            saveBtn.addEventListener('click', saveCalculation);
            
            // Initial calculation if car is selected
            <?php if ($selected_car): ?>
                setTimeout(calculatePayment, 100);
            <?php endif; ?>
            
            function calculatePayment() {
                const carPrice = parseFloat(document.getElementById('car_price').value) || 0;
                const downPayment = parseFloat(document.getElementById('down_payment').value) || 0;
                const loanTerm = parseInt(document.getElementById('loan_term').value) || 0;
                const interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;
                
                // Validation
                if (carPrice <= 0 || loanTerm <= 0 || interestRate < 0) {
                    showResults(0, 0, 0, carPrice, downPayment, loanTerm, interestRate);
                    return;
                }
                
                if (downPayment >= carPrice) {
                    alert('Down payment cannot be greater than or equal to car price.');
                    return;
                }
                
                // Calculate loan amount
                const loanAmount = carPrice - downPayment;
                
                if (loanAmount <= 0) {
                    showResults(0, 0, 0, carPrice, downPayment, loanTerm, interestRate);
                    return;
                }
                
                // Calculate monthly payment using loan formula
                const monthlyRate = (interestRate / 100) / 12;
                const numPayments = loanTerm * 12;
                
                let monthlyPayment;
                if (monthlyRate === 0) {
                    // No interest case
                    monthlyPayment = loanAmount / numPayments;
                } else {
                    // Standard loan formula: M = P * [r(1+r)^n] / [(1+r)^n - 1]
                    monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) / 
                                   (Math.pow(1 + monthlyRate, numPayments) - 1);
                }
                
                const totalPayment = monthlyPayment * numPayments;
                const totalInterest = totalPayment - loanAmount;
                
                showResults(monthlyPayment, totalPayment + downPayment, totalInterest, carPrice, downPayment, loanTerm, interestRate);
                
                // Store calculation data for saving
                window.currentCalculation = {
                    car_price: carPrice,
                    down_payment: downPayment,
                    loan_term: loanTerm,
                    interest_rate: interestRate,
                    monthly_payment: monthlyPayment,
                    total_payment: totalPayment + downPayment,
                    total_interest: totalInterest,
                    car_id: document.querySelector('input[name="car_id"]').value || null
                };
            }
            
            function showResults(monthlyPayment, totalPayment, totalInterest, carPrice, downPayment, loanTerm, interestRate) {
                // Update result displays
                document.getElementById('monthlyPaymentValue').textContent = formatCurrency(monthlyPayment);
                document.getElementById('totalPaymentValue').textContent = formatCurrency(totalPayment);
                document.getElementById('totalInterestValue').textContent = formatCurrency(totalInterest);
                
                // Update breakdown
                const breakdown = document.getElementById('paymentBreakdown');
                if (monthlyPayment > 0) {
                    document.getElementById('principalAmount').textContent = formatCurrency(carPrice);
                    document.getElementById('downPaymentDisplay').textContent = formatCurrency(downPayment);
                    document.getElementById('loanAmount').textContent = formatCurrency(carPrice - downPayment);
                    document.getElementById('interestRateDisplay').textContent = interestRate + '%';
                    document.getElementById('loanTermDisplay').textContent = loanTerm + ' years';
                    breakdown.style.display = 'block';
                } else {
                    breakdown.style.display = 'none';
                }
                
                // Enable/disable save button
                saveBtn.disabled = monthlyPayment <= 0;
            }
            
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }
            
            function saveCalculation() {
                if (!window.currentCalculation) {
                    alert('Please calculate a payment first.');
                    return;
                }
                
                const formData = new FormData();
                formData.append('action', 'save_calculation');
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                
                // Add calculation data
                Object.keys(window.currentCalculation).forEach(key => {
                    formData.append(key, window.currentCalculation[key]);
                });
                
                // Disable button and show loading
                saveBtn.disabled = true;
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '⏳ Saving...';
                
                fetch('calculator.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Calculation saved successfully!', 'success');
                    } else {
                        showMessage(data.message || 'Failed to save calculation.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('An error occurred while saving.', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                });
            }
            
            function showMessage(message, type) {
                // Create message element
                const messageDiv = document.createElement('div');
                messageDiv.className = `message-toast ${type}`;
                messageDiv.innerHTML = message;
                messageDiv.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
                    color: ${type === 'success' ? '#155724' : '#721c24'};
                    padding: 1rem;
                    border-radius: 6px;
                    border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
                    z-index: 1000;
                    animation: slideIn 0.3s ease-out;
                `;
                
                document.body.appendChild(messageDiv);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    messageDiv.remove();
                }, 3000);
            }
        });
        
        // CSS for message animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>