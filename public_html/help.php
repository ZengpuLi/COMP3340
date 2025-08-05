<?php
/**
 * Help & Documentation Page
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-fixed.php';
require_once 'php/navigation-fixed.php';

// Define help topics
$help_topics = [
    'register-login' => [
        'title' => 'Registration and Login',
        'description' => 'Learn how to create an account and login to the system',
        'content' => [
            '1. Click the "Register" link in the navigation bar',
            '2. Fill in your username, email, and password',
            '3. Confirm your password and submit the form',
            '4. Use your credentials to login'
        ]
    ],
    'search-cars' => [
        'title' => 'Search and Browse Cars',
        'description' => 'Learn how to find the vehicles you want',
        'content' => [
            '1. Visit the "Cars" page to view all vehicles',
            '2. Browse through various car options',
            '3. View detailed car information',
            '4. Use filters to narrow down your search'
        ]
    ],
    'contact-sellers' => [
        'title' => 'Contact Sellers',
        'description' => 'Learn how to get in touch with our sales team',
        'content' => [
            '1. Click the "Inquire" button on cars you are interested in',
            '2. Fill out the inquiry form',
            '3. Provide your contact information',
            '4. Wait for our team to respond'
        ]
    ],
    'loan-calculator' => [
        'title' => 'Loan Calculator',
        'description' => 'Learn how to use our loan calculation tool',
        'content' => [
            '1. Visit the "Loan Calculator" page',
            '2. Enter the vehicle price',
            '3. Set your down payment amount',
            '4. Choose loan term and interest rate',
            '5. View your monthly payment amount'
        ]
    ],
    'theme-switcher' => [
        'title' => 'Theme Switcher',
        'description' => 'Learn how to change the website appearance',
        'content' => [
            '1. Find the theme selector at the top of the page',
            '2. Select a theme from the dropdown menu',
            '3. The theme will be applied immediately',
            '4. Your choice will be saved'
        ]
    ]
];

// Get selected topic from URL
$selected_topic = $_GET['topic'] ?? 'index';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - AutoDeals</title>
    <link rel="stylesheet" href="css/theme-default.css" id="theme-link">
    
    <style>
        .help-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .help-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }
        
        .help-sidebar {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .help-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .help-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .help-nav li {
            margin-bottom: 0.5rem;
        }
        
        .help-nav a {
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: var(--text-dark);
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .help-nav a:hover,
        .help-nav a.active {
            background: var(--primary-color);
            color: white;
        }
        
        .topic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .topic-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .topic-card h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .topic-content ol {
            padding-left: 1.5rem;
        }
        
        .topic-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .help-container {
                grid-template-columns: 1fr;
                gap: 1rem;
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

    <?php echo generateNavigation('help.php'); ?>

    <main>
        <section class="help-hero">
            <h2>📚 Help & Support</h2>
            <p>Find answers to all your questions about using our website</p>
        </section>

        <div class="help-container">
            <aside class="help-sidebar">
                <h3>Help Topics</h3>
                <ul class="help-nav">
                    <li><a href="help.php" <?php echo $selected_topic === 'index' ? 'class="active"' : ''; ?>>📋 Help Overview</a></li>
                    <?php foreach ($help_topics as $key => $topic): ?>
                        <li><a href="help.php?topic=<?php echo $key; ?>" <?php echo $selected_topic === $key ? 'class="active"' : ''; ?>>
                            <?php echo sanitizeOutput($topic['title']); ?>
                        </a></li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <div class="help-content">
                <?php if ($selected_topic === 'index'): ?>
                    <h2>🏠 Welcome to AutoDeals Help Center</h2>
                    <p>Welcome to our comprehensive help center! Here you'll find detailed guides and tutorials to help you make the most of our used car purchase website.</p>
                    
                    <h3>Quick Start Guide:</h3>
                    <ol>
                        <li><strong>Browse Cars:</strong> Visit our Cars page to see our inventory</li>
                        <li><strong>Create Account:</strong> Register for personalized features</li>
                        <li><strong>Calculate Payments:</strong> Use our loan calculator</li>
                        <li><strong>Contact Us:</strong> Send inquiries about vehicles</li>
                        <li><strong>Customize Experience:</strong> Switch between themes</li>
                    </ol>

                    <div class="topic-grid">
                        <?php foreach ($help_topics as $key => $topic): ?>
                            <div class="topic-card">
                                <h3><?php echo sanitizeOutput($topic['title']); ?></h3>
                                <p><?php echo sanitizeOutput($topic['description']); ?></p>
                                <a href="help.php?topic=<?php echo $key; ?>" style="color: var(--primary-color); font-weight: 600;">Learn More →</a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif (isset($help_topics[$selected_topic])): ?>
                    <?php $topic = $help_topics[$selected_topic]; ?>
                    <h2><?php echo sanitizeOutput($topic['title']); ?></h2>
                    <p class="topic-description"><?php echo sanitizeOutput($topic['description']); ?></p>
                    
                    <div class="topic-content">
                        <h3>Step-by-Step Instructions:</h3>
                        <ol>
                            <?php foreach ($topic['content'] as $step): ?>
                                <li><?php echo sanitizeOutput($step); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>

                    <div style="margin-top: 2rem; padding: 1rem; background: #e3f2fd; border-radius: 6px;">
                        <strong>💡 Need More Help?</strong><br>
                        If you still have questions, please visit our <a href="contact.php">Contact page</a> or send us an <a href="inquiry.php">inquiry</a>.
                    </div>

                <?php else: ?>
                    <h2>❌ Topic Not Found</h2>
                    <p>Sorry, the help topic you're looking for doesn't exist.</p>
                    <a href="help.php" style="color: var(--primary-color); font-weight: 600;">← Back to Help Overview</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/theme-switcher.js"></script>
</body>
</html>