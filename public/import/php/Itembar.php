<?php
    $last_open = $_SESSION['last_gift'] ?? null;
    $today = date('Y-m-d');

    $showGift = ($last_open !== $today);
    ?>

<div class="image-container">
    <?php if ($showGift): ?>
        <div class="image-name">🎁</div>
            <div class="image-wrapper" id="gift">
                <img src="https://production-gameflipusercontent.fingershock.com/us-east-1:283d9bf8-ddfd-4a29-82be-3e03e84897dc/932f60c4-6f5b-433c-b5ca-9e1979faf095/041aedbd-70d5-4996-98f4-1e3ba9d4175a" alt="Discord Image 2">
                <div class="image-number" style="background-color: #da5920;">ouvrir</div>
            </div>
        </div>
    <?php endif; ?>
    <div class="image-container">
        <div class="image-name">XP</div>
            <div class="image-wrapper">
                <img src="https://production-gameflipusercontent.fingershock.com/us-east-1:283d9bf8-ddfd-4a29-82be-3e03e84897dc/932f60c4-6f5b-433c-b5ca-9e1979faf095/041aedbd-70d5-4996-98f4-1e3ba9d4175a" alt="Discord Image 2">
                <div class="image-number">
                    <?php
                    $bloc = 'level';
                    include __DIR__ . '/../../../requiments/ItemsData.php';
                    ?>
                </div>
            </div>
        </div>
    <div class="image-container">
        <div class="image-name">S-Coin</div>
            <div class="image-wrapper">
                <img src="/public/img/icon/coin.png" alt="Discord Image 3">
                <div class="image-number">
                    <?php
                    $bloc = 'etoile';
                    include __DIR__ . '/../../../requiments/ItemsData.php';
                    ?>
                </div>
            </div>
        </div>
        <!-- Ajoutez d'autres items 
    </div>
</div> -->
