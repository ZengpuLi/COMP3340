<?php
/**
 * Loan Calculator Page
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-fixed.php';
require_once 'php/navigation-fixed.php';

// Get car info if car_id is provided
$selected_car = null;
if (isset($_GET['car_id'])) {
    try {
        $conn = getDatabaseConnection();
        $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->bind_param("i", $_GET['car_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $selected_car = $result->fetch_assoc();
        $conn->close();
    } catch (Exception $e) {
        // Handle error silently
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculator - AutoDeals</title>
    <link rel="stylesheet" href="css/theme-default.css" id="theme-link">
    
    <style>
        .calculator-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .calculator-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .calculator-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .result-card {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }
        
        .monthly-payment {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .result-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }
        
        .detail-item {
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 1rem;
        }
        
        .detail-value {
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .calculator-container {
                padding: 1rem;
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
        <div class="logo-text">🚗 AutoDeals</div>
        <h1>Used Car Purchase Website</h1>
        <?php echo generateHeaderGreeting(); ?>
    </header>

    <?php echo generateNavigation('calculator.php'); ?>

    <main>
        <section class="calculator-hero">
            <h2>💰 Loan Payment Calculator</h2>
            <p>Calculate your monthly car loan payments instantly</p>
        </section>

        <div class="calculator-container">
            <?php if ($selected_car): ?>
                <div class="calculator-card">
                    <h3>🚗 Selected Vehicle</h3>
                    <p><strong><?php echo sanitizeOutput($selected_car['name']); ?></strong></p>
                    <p>Price: <strong><?php echo formatPrice($selected_car['price']); ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="calculator-card">
                <h3>Loan Details</h3>
                <form id="loan-calculator">
                    <div class="form-group">
                        <label for="car-price">Car Price ($)</label>
                        <input type="number" id="car-price" name="car_price" 
                               value="<?php echo $selected_car ? $selected_car['price'] : '20000'; ?>" 
                               min="1000" max="100000" step="100" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="down-payment">Down Payment ($)</label>
                            <input type="number" id="down-payment" name="down_payment" 
                                   value="<?php echo $selected_car ? round($selected_car['price'] * 0.1) : '2000'; ?>" 
                                   min="0" step="100">
                        </div>
                        <div class="form-group">
                            <label for="loan-term">Loan Term</label>
                            <select id="loan-term" name="loan_term">
                                <option value="12">1 year</option>
                                <option value="24">2 years</option>
                                <option value="36">3 years</option>
                                <option value="48" selected>4 years</option>
                                <option value="60">5 years</option>
                                <option value="72">6 years</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="interest-rate">Annual Interest Rate (%)</label>
                        <input type="number" id="interest-rate" name="interest_rate" 
                               value="5.5" min="0" max="25" step="0.1" required>
                    </div>

                    <button type="button" onclick="calculatePayment()" class="btn">Calculate Payment</button>
                </form>

                <div id="calculation-result" class="result-card" style="display: none;">
                    <div class="monthly-payment" id="monthly-amount">$0</div>
                    <p>Monthly Payment</p>
                    
                    <div class="result-details">
                        <div class="detail-item">
                            <div class="detail-value" id="total-interest">$0</div>
                            <div>Total Interest</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-value" id="total-payment">$0</div>
                            <div>Total Payment</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-value" id="loan-amount">$0</div>
                            <div>Loan Amount</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="calculator-card">
                <h3>📋 How to Use This Calculator</h3>
                <ol>
                    <li><strong>Enter Car Price:</strong> The total price of the vehicle you want to purchase</li>
                    <li><strong>Set Down Payment:</strong> Amount you'll pay upfront (typically 10-20% of car price)</li>
                    <li><strong>Choose Loan Term:</strong> How many years you want to pay off the loan</li>
                    <li><strong>Enter Interest Rate:</strong> Annual percentage rate from your lender</li>
                    <li><strong>Calculate:</strong> Click the button to see your monthly payment</li>
                </ol>
                
                <p><strong>💡 Tip:</strong> A larger down payment or shorter loan term will reduce your monthly payments and total interest paid.</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/theme-switcher.js"></script>
    
    <script>
        function calculatePayment() {
            // Get form values
            const carPrice = parseFloat(document.getElementById('car-price').value) || 0;
            const downPayment = parseFloat(document.getElementById('down-payment').value) || 0;
            const loanTerm = parseInt(document.getElementById('loan-term').value) || 48;
            const interestRate = parseFloat(document.getElementById('interest-rate').value) || 5.5;
            
            // Calculate loan amount
            const loanAmount = carPrice - downPayment;
            
            if (loanAmount <= 0) {
                alert('Loan amount must be positive. Please check your car price and down payment.');
                return;
            }
            
            // Calculate monthly interest rate
            const monthlyRate = (interestRate / 100) / 12;
            
            // Calculate monthly payment using loan formula
            let monthlyPayment;
            if (monthlyRate === 0) {
                monthlyPayment = loanAmount / loanTerm;
            } else {
                monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, loanTerm)) / 
                                (Math.pow(1 + monthlyRate, loanTerm) - 1);
            }
            
            // Calculate totals
            const totalPayment = monthlyPayment * loanTerm;
            const totalInterest = totalPayment - loanAmount;
            
            // Display results
            document.getElementById('monthly-amount').textContent = formatCurrency(monthlyPayment);
            document.getElementById('loan-amount').textContent = formatCurrency(loanAmount);
            document.getElementById('total-interest').textContent = formatCurrency(totalInterest);
            document.getElementById('total-payment').textContent = formatCurrency(totalPayment);
            
            // Show result card
            document.getElementById('calculation-result').style.display = 'block';
            document.getElementById('calculation-result').scrollIntoView({ behavior: 'smooth' });
        }
        
        function formatCurrency(amount) {
            return '$' + Math.round(amount).toLocaleString();
        }
        
        // Calculate on page load if values are present
        window.addEventListener('load', function() {
            calculatePayment();
        });
        
        // Recalculate when inputs change
        document.querySelectorAll('#loan-calculator input, #loan-calculator select').forEach(input => {
            input.addEventListener('input', calculatePayment);
            input.addEventListener('change', calculatePayment);
        });
    </script>
</body>
</html>