<?php
session_start();

include 'requiments/Counter/messages.php';
include __DIR__ . '/requiments/UsersData.php';
include 'requiments/Counter/friends.php';
include __DIR__ . '/requiments/get_theme.php';
include 'requiments/Auth.php';


$isUserLoggedIn = isset($_SESSION['user_id']);

$sql = "
    SELECT u.id, u.prenom, p.image_content
    FROM utilisateur u
    JOIN relation r ON (r.demandeur = u.id OR r.receveur = u.id)
    LEFT JOIN photos_de_profil p ON u.id = p.user_id
    WHERE (r.demandeur = ? OR r.receveur = ?) 
    AND r.statut = 1 
    AND u.id != ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $user_id, $user_id]);
$amis = $stmt->fetchAll(PDO::FETCH_ASSOC);

function displayImage($blob) {
    if ($blob) {
        return 'data:image/jpeg;base64,' . base64_encode($blob);
    }
    return 'default.jpg';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/Import/icons/Logo.png">
    <title>SYSMI PROJECT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	 <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="manifest" href="/Import/assets/manifest.json">
    <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
</head>
<style>
	#gift {
	background: linear-gradient(45deg, #ffd700, #ffa500);
    text-align: center;
    box-shadow: 0 0 10px #ff0;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 5px #ff0; }
    50% { box-shadow: 0 0 20px #ff0; }
    100% { box-shadow: 0 0 5px #ff0; }
}

</style>
	<body>
		
		<nav>
		<div class="logo">
			<img src="/public/img/icon/Logo.png" alt="Logo de Mon Réseau" class="logo-img">
			<span class="logo-text">the SYSMI</span>
		</div>
		<a href="/public/pages/miscellaneous/post.php" class="menu-item">
			<i class="fas fa-home"></i>
			<span>VOS POST</span>
		</a>
		<a href="/public/pages/miscellaneous/new_post.php" class="menu-item" id="home-link" onclick="refreshPosts();">
		<i class="fas fa-plus-circle"></i>
      <span>NEW POST</span>
    </a>
		<a href="/public/pages/miscellaneous/FriendList.php" class="menu-item">
			<i class="fas fa-bell"></i>
			<span>S - NOTIFY</span>
			<?php if ($nbDemandes > 0): ?>
				<div class="badge"><?php echo $nbDemandes; ?></div>
			<?php endif; ?>
		</a>
		<?php if (!empty($role) && ($role === 'premium' || $role === 'staff')): ?>
		<a href="/public/pages/miscellaneous/dashboard.php" class="menu-item" style="background: darkgoldenrod;">
			<i class="fas fa-users"></i>
			<span>dashboard</span>
		</a>
			</div>
	<?php endif; ?>
	</nav>
		<div class="main">  
			<div class="container section <?= !$isUserLoggedIn ? 'not-logged' : '' ?>" id="friends">
			<?php if ($isUserLoggedIn): ?>
			  <?php if (count($amis) > 0): ?>
				<?php foreach ($amis as $ami): ?>
				  <div class="friend-item">
					<a href="/public/import/php/account.php?id=<?= $ami['id'] ?>" class="friend-profil">
					  <img src="<?= displayImage($ami['image_content']) ?>" alt="Photo de profil" class="profile-pic">
					  <div class="friend-info">
						<div class="friend-name">
						  <?= htmlspecialchars($ami['prenom']) ?>
						</div>
					  </div>
					</a>
				  </div>
				<?php endforeach; ?>
			  <?php else: ?>
				<p style="color: red; font-weight: 700;">Vous n'avez pas encore d'amis.</p>
			  <?php endif; ?>
			<?php else: ?>
			  <div class="not-logged-message">
				<h2>Liste d'amis</h2>
				<p>Connecte-toi pour voir tes amis ici !</p>
			  </div>
			<?php endif; ?>
		  </div>
		  
		<div class="container" id="profil">
			<div class="profile-card">
				<a href="/public/import/php/account.php?id=<?php echo $user_id; ?>&prenom=<?php echo urlencode($prenom); ?>" class="profil-link">
					<?php if (!empty($image_content)): ?>
						<img src="data:image/jpeg;base64,<?php echo base64_encode($image_content); ?>" alt="Avatar">
					<?php else: ?>
						<img src="https://p7.hiclipart.com/preview/782/114/405/5bbc3519d674c.jpg" alt="Profile Picture" class="profile-								picture">
					<?php endif; ?>
					<div  style="width: 100%">
						<?php if ($isUserLoggedIn): ?>
							<h1><?php echo htmlspecialchars($prenom); ?></h1>
						    	<!-- ! -->  <div class="thought-bubble">
						        		<div class="status">🎭 Humeur : En mode chill 😎</div>
      						  			<div class="quote">📝 Citation du jour : "Chaque petite action compte."</div>
								</div> 
							<p>Appuyez ici pour modifier votre profil</p>
							<div class="badge">
								<i class="fas fa-crown" style="color: goldenrod;"></i>
							</div>
						<?php else: ?>
							<h1>Bienvenue,</h1>
							<p>Veuillez vous connecter.</p>
						<?php endif; ?>
					</div>
				</a>
                <div class="links-buttons">
                    <?php if ($isUserLoggedIn): ?>
						<a href="https://discord.gg/83pzTuJ5wv" class="links-logo">
                            <span>Nous rejoindre</span> <i class="fab fa-discord"></i>
                        </a>
					    <a href="/public/pages/miscellaneous/Settings.php" class="links-logo">
                            <span>paramètres</span> <i class="fas fa-cog"></i>
                        </a>
						<a href="/public/pages/miscellaneous/UserList.php" class="links-logo">
                            <span>add user</span> <i class="fas fa-user-plus"></i>
                        </a>
						<a targat="_blank" href="https://github.com/zacaventure/SYSMI_PROJECT/releases/tag/1.0.0" class="links-logo">
                            <span>installer</span> <i class="fab fa-github"></i>
                        </a>
						<a href="#feedback" class="links-logo">
						  <span>noter le site</span> <i class="fas fa-star"></i>
						</a>
                    <?php else: ?>
                        <a href="/public/forms/signup.html" class="links-logo">
                            Signup <i class="fas fa-user-plus" style="display: block;"></i>
                        </a>
                        <a href="/public/forms/login.html" class="links-logo">
                            login <i class="fa-solid fa-right-to-bracket" style="display: block;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
			
			<!-- !
			<div class="sysmi-widget">
			  <div class="rank">Explorateur Niv. 12</div>

			  <div class="stats">
				<div><span>⭐ 245</span>Étoiles</div>
				<div><span>🔋 78%</span>Énergie</div>
				<div><span>🎯 3</span>Missions</div>
			  </div>
			  <div class="mission">Mission actuelle : Aider 2 membres à se connecter</div>
			</div>
			----->

		<div class="penel">
			<div class="menu-toggle" onclick="toggleMenu()">☰</div>
				<div class="menu" id="menu">
					<a href="/public/import/php/script/forms/logout.php">Se déconnecter</a>
					<a href="https://discord.gg/83pzTuJ5wv">Notre discord</a>
					<a href="#">Services</a>
					<a href="#">Contact</a>
				</div>
			</div>
		</div>

        
<div class="container" id="itembar">
    <?php include 'public/import/php/itembar.php'; ?>
</div>

			
			<div class="container" id="objects">
				<div class="card"></div>
				<div class="card"></div>
				<div class="card"></div>
			</div>

        <div class="container" id="utilisateurs">
                <div class="user-list">
                    <a href="/public/pages/miscellaneous/minigames.php" style="background-image: url('/Import/icons/game.png'); background-size: cover; background-repeat: no-repeat; background-position: center;" class="user">
                        <img src="https://getdrawings.com/free-icon/snake-game-icon-66.png" alt="User Avatar">
                        <p>Mini jeux</p>
                    </a>
					
                    <a href="/public/pages/miscellaneous/boutique.php" style="background-image: url('https://wallpaperaccess.com/full/2338290.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center;" class="user">
                        <img src="https://static.vecteezy.com/system/resources/previews/014/494/586/original/shopping-bags-colorful-paper-bags-for-shopping-mall-products-png.png" alt="User Avatar">
                        <p>Boutique</p>
                    </a>
					
                    <a href="/public/pages/miscellaneous/Services.php" style="background-image: url('/Import/icons/aide.png'); background-size: cover; background-repeat: no-repeat; background-position: center;" class="user">
                        <img src="public/img/icon/service.png" alt="User Avatar">
                        <p>Aide / Services</p>
                    </a>
                </div>
            </div>

        <div class="container" id="feedback">
        	<section class="section" id="testimonials">
            	<h2>
                	<a href="/public/pages/miscellaneous/feedback.php" style="margin-left: 10px; font-size: 16px; text-decoration: none; color: #ffffff; padding: 5px 10px; border-radius: 5px; transition: background-color 0.3s; display: flex; justify-content: center; gap: 10px; background-color: #ff0062;">
                    	Tout voir <i class="fas fa-arrow-right"></i>
                    </a>
                </h2>
            <form id="feedback-form" class="feedback-form" method="POST" action="/public/import/php/script/forms/feedback.php">
                
                <input type="hidden" id="user-id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                <label for="feedback">Votre avis :</label>
                <textarea id="feedback" name="feedback" placeholder="Partagez votre expérience ici..." rows="4" required></textarea>

                <label for="rating">Votre note :</label>
                <select id="rating" name="rating" required>
                    <option value="">-- Choisissez une note --</option>
                    <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                    <option value="4">⭐⭐⭐⭐ - Très bien</option>
                    <option value="3">⭐⭐⭐ - Correct</option>
                    <option value="2">⭐⭐ - Médiocre</option>
                    <option value="1">⭐ - Mauvais</option>
                </select>
                <button type="submit">Envoyer mon avis</button>
            </form>
        </div>
    </div>
    <footer>
        &copy; 2025 La SYSMI PROJECT. <a href="#contact">Politique de confidentialité</a>.
    </footer>

    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/Import/assets/sw.js').then((registration) => {
            console.log('Service Worker registered:', registration);
        }).catch((error) => {
            console.error('Service Worker registration failed:', error);
        });
    }
		
        function toggleMenu() {
            var menu = document.getElementById("menu");
            if (menu.classList.contains("active")) {
                menu.classList.remove("active");
                setTimeout(() => {
                    menu.style.display = "none";
                }, 300);
            } else {
                menu.style.display = "flex";
                setTimeout(() => {
                    menu.classList.add("active");
                }, 10);
            }
        }
</script>
</body>
</html>
