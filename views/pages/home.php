<?php
// Start the session or include any necessary
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Plateforme Cameroun</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <a href="home.php" class="logo">
                <span>Plateforme Cameroun</span>
            </a>
            <nav class="header-nav">
                <a href="home.php" class="nav-link">Accueil</a>
                <a href="services.php" class="nav-link">Services</a>
                <a href="about.php" class="nav-link">À propos</a>
                <div class="user-menu">
                    <span class="user-greeting">Bonjour, Utilisateur</span>
                    <a href="profile.php" class="nav-link">Profil</a>
                    <a href="logout.php" class="nav-link">Déconnexion</a>
                </div>
            </nav>
            <button class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div>
                <h1>Bienvenue sur la Plateforme Cameroun</h1>
                <p class="hero-subtitle">Découvrez des services rapides, fiables et accessibles partout au Cameroun.</p>
                <div class="hero-features">
                    <div class="feature">
                        <span class="feature-icon">🌍</span>
                        <span>Couverture nationale</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">🚚</span>
                        <span>Livraison rapide</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">🔒</span>
                        <span>Paiements sécurisés</span>
                    </div>
                    <div class="feature">
                        <span class="feature-icon">🛠️</span>
                        <span>Support 24/7</span>
                    </div>
                </div>
                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary btn-large">S'inscrire</a>
                    <a href="login.php" class="btn btn-outline btn-large">Se connecter</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="hero-img">
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2>Comment ça fonctionne</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Inscription</h3>
                    <p>Créez votre compte en quelques clics pour accéder à nos services.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Commande</h3>
                    <p>Choisissez vos produits et passez votre commande en ligne.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Livraison</h3>
                    <p>Recevez vos produits rapidement à votre adresse.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Coverage Section -->
    <section class="coverage">
        <div class="container">
            <h2>Nos zones de couverture</h2>
            <div class="cities-grid">
                <div class="city-card">
                    <h3>Yaoundé</h3>
                    <p>Services disponibles dans toute la capitale.</p>
                    <span class="availability btn-success">Disponible</span>
                </div>
                <div class="city-card">
                    <h3>Douala</h3>
                    <p>Livraison rapide dans la ville économique.</p>
                    <span class="availability btn-success">Disponible</span>
                </div>
                <div class="city-card">
                    <h3>Bamenda</h3>
                    <p>Services en cours de déploiement.</p>
                    <span class="availability btn-warning">Bientôt disponible</span>
                </div>
                <div class="city-card">
                    <h3>Garoua</h3>
                    <p>Commandez dès maintenant dans le Nord.</p>
                    <span class="availability btn-success">Disponible</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Plateforme Cameroun</h3>
                    <p>Connecter le Cameroun avec des services modernes et accessibles.</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="home.php">Accueil</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="about.php">À propos</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li><a href="mailto:support@plateformecameroun.cm">support@plateformecameroun.cm</a></li>
                        <li><a href="tel:+237123456789">+237 123 456 789</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Plateforme Cameroun. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Mobile Menu -->
    <script>
        document.querySelector('.mobile-menu-toggle').addEventListener('click', () => {
            const nav = document.querySelector('.header-nav');
            const toggle = document.querySelector('.mobile-menu-toggle');
            nav.classList.toggle('active');
            toggle.classList.toggle('active');
        });
    </script>
</body>
</html>
