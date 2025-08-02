<?php
/**
 * Help & Documentation
 * Used Car Purchase Website - User Guide and Support
 */

// Include session management and configuration
require_once '../php/session.php';
require_once '../php/navigation.php';
require_once '../php/config.php';
require_once '../php/seo.php';

// Get help topic from URL parameter
$topic = sanitizeInput($_GET['topic'] ?? 'index');
$valid_topics = ['index', 'register-login', 'search-cars', 'contact-sellers', 'loan-calculator', 'theme-switcher'];

if (!in_array($topic, $valid_topics)) {
    $topic = 'index';
}

// Generate SEO meta tags
$seo_config = getSEOConfig('help.php', [
    'title' => $topic === 'index' ? 'Help & Support - AutoDeals Customer Service' : 'Help: ' . ucfirst(str_replace('-', ' ', $topic)) . ' - AutoDeals Support',
    'description' => $topic === 'index' ? 'Find answers to frequently asked questions about buying used cars, financing, warranties, and our services at AutoDeals.' : 'Step-by-step guide for ' . str_replace('-', ' ', $topic) . ' on the AutoDeals website.',
    'canonical' => '/help.php' . ($topic !== 'index' ? '?topic=' . $topic : ''),
    'robots' => 'index, follow'
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php echo generateSEOMetaTags($seo_config); ?>
    <link rel="stylesheet" href="../css/theme-default.css" id="theme-link">
    
    <style>
        /* Help-specific styles */
        .help-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .help-hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .help-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .help-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .help-sidebar {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }
        
        .help-content {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            min-height: 600px;
        }
        
        .help-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .help-nav li {
            margin-bottom: 0.75rem;
        }
        
        .help-nav a {
            display: block;
            padding: 1rem;
            text-decoration: none;
            color: var(--text-dark);
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            font-weight: 500;
        }
        
        .help-nav a:hover {
            background: var(--light-gray);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .help-nav a.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .help-nav .icon {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        .help-section h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
        }
        
        .help-section h4 {
            color: var(--secondary-color);
            margin: 2rem 0 1rem 0;
            font-size: 1.3rem;
        }
        
        .help-steps {
            counter-reset: step-counter;
            padding-left: 0;
            list-style: none;
        }
        
        .help-steps li {
            counter-increment: step-counter;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--light-gray);
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
            position: relative;
        }
        
        .help-steps li::before {
            content: counter(step-counter);
            position: absolute;
            left: -2px;
            top: -10px;
            background: var(--primary-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .help-steps .step-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .help-steps .step-description {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .help-screenshot {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            margin: 1rem 0;
        }
        
        .help-tip {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
            color: #1565c0;
        }
        
        .help-tip::before {
            content: "💡 ";
            font-size: 1.2rem;
        }
        
        .help-warning {
            background: #fff3e0;
            border: 1px solid #ffcc02;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
            color: #ef6c00;
        }
        
        .help-warning::before {
            content: "⚠️ ";
            font-size: 1.2rem;
        }
        
        .help-success {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
            color: #2e7d32;
        }
        
        .help-success::before {
            content: "✅ ";
            font-size: 1.2rem;
        }
        
        .help-topics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .topic-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .topic-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
            border-color: var(--primary-color);
        }
        
        .topic-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .topic-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .topic-description {
            color: var(--text-light);
            line-height: 1.5;
        }
        
        .breadcrumb {
            background: var(--light-gray);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .help-hero {
                padding: 2rem 1rem;
            }
            
            .help-hero h2 {
                font-size: 2rem;
            }
            
            .help-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .help-sidebar {
                position: static;
                order: 2;
            }
            
            .help-content {
                order: 1;
            }
            
            .help-steps li {
                padding: 1rem;
            }
            
            .help-topics-grid {
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

    <?php echo generateNavigation('help.php'); ?>

    <main>
        <section class="help-hero">
            <h2>📚 Help & Support Center</h2>
            <p>Find answers to your questions and learn how to make the most of AutoDeals. We're here to help you every step of the way!</p>
        </section>

        <?php if ($topic !== 'index'): ?>
        <div class="breadcrumb">
            <a href="help.php">📚 Help Center</a> → <?php echo ucfirst(str_replace('-', ' ', $topic)); ?>
        </div>
        <?php endif; ?>

        <div class="help-container">
            <aside class="help-sidebar">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">📖 Help Topics</h3>
                <ul class="help-nav">
                    <li><a href="help.php" class="<?php echo $topic === 'index' ? 'active' : ''; ?>">
                        <span class="icon">🏠</span>Help Center Home
                    </a></li>
                    <li><a href="help.php?topic=register-login" class="<?php echo $topic === 'register-login' ? 'active' : ''; ?>">
                        <span class="icon">👤</span>Register & Login
                    </a></li>
                    <li><a href="help.php?topic=search-cars" class="<?php echo $topic === 'search-cars' ? 'active' : ''; ?>">
                        <span class="icon">🔍</span>Search & Browse Cars
                    </a></li>
                    <li><a href="help.php?topic=contact-sellers" class="<?php echo $topic === 'contact-sellers' ? 'active' : ''; ?>">
                        <span class="icon">📧</span>Contact Sellers
                    </a></li>
                    <li><a href="help.php?topic=loan-calculator" class="<?php echo $topic === 'loan-calculator' ? 'active' : ''; ?>">
                        <span class="icon">💰</span>Loan Calculator
                    </a></li>
                    <li><a href="help.php?topic=theme-switcher" class="<?php echo $topic === 'theme-switcher' ? 'active' : ''; ?>">
                        <span class="icon">🎨</span>Theme Switcher
                    </a></li>
                </ul>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <h4 style="color: var(--secondary-color); margin-bottom: 1rem;">📞 Need More Help?</h4>
                    <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;">Can't find what you're looking for? Contact our support team!</p>
                    <a href="contact.html" style="display: inline-block; background: var(--secondary-color); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Contact Support</a>
                </div>
            </aside>

            <div class="help-content">
                <?php
                switch ($topic) {
                    case 'index':
                        include 'help_sections/help_index.php';
                        break;
                    case 'register-login':
                        include 'help_sections/help_register_login.php';
                        break;
                    case 'search-cars':
                        include 'help_sections/help_search_cars.php';
                        break;
                    case 'contact-sellers':
                        include 'help_sections/help_contact_sellers.php';
                        break;
                    case 'loan-calculator':
                        include 'help_sections/help_loan_calculator.php';
                        break;
                    case 'theme-switcher':
                        include 'help_sections/help_theme_switcher.php';
                        break;
                    default:
                        include 'help_sections/help_index.php';
                }
                ?>
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
        // Help page interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            // Copy code snippets functionality
            const codeBlocks = document.querySelectorAll('pre code');
            codeBlocks.forEach(block => {
                const button = document.createElement('button');
                button.textContent = 'Copy';
                button.style.cssText = 'position: absolute; top: 5px; right: 5px; background: var(--primary-color); color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; cursor: pointer;';
                
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                block.parentNode.insertBefore(wrapper, block);
                wrapper.appendChild(block);
                wrapper.appendChild(button);
                
                button.addEventListener('click', () => {
                    navigator.clipboard.writeText(block.textContent).then(() => {
                        button.textContent = 'Copied!';
                        setTimeout(() => button.textContent = 'Copy', 2000);
                    });
                });
            });
            
            // Help section analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'help_page_view', {
                    'event_category': 'help',
                    'event_label': '<?php echo $topic; ?>',
                    'help_section': '<?php echo $topic; ?>'
                });
            }
        });
    </script>
</body>
</html>