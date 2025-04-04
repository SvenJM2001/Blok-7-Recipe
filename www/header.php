<?php
require 'database.php';

if (isset($_SESSION['gebruikersnaam'])) {
    // Haal de rol van de ingelogde gebruiker op
    $gebruikersnaam = $_SESSION['gebruikersnaam'];
    $query = "SELECT rol FROM gebruikers WHERE gebruikersnaam = :gebruikersnaam";
    $stmt = $conn->prepare($query);
    $stmt->bindvalue(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Als de gebruiker bestaat, haal de rol op
    if ($row) {
        $rol = $row['rol'];
    } else {
        $rol = '';
    }
} else {
    $rol = '';
}
?>
<header>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo">
                <a href="index.php">Recepten</a>
            </div>
            <ul class="nav-links">
                <?php if ($rol === 'beheerder') { ?>
                    <li class="dropdown">
                        <a href="#">Recepten</a>
                        <div class="dropdown-content">
                            <a href="recepten_index.php">Bekijken</a>
                            <a href="recepten_add.php">Toevoegen</a>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a href="#">Ingredienten</a>
                        <div class="dropdown-content">
                            <a href="ingredienten_index.php">Bekijken</a>
                            <a href="ingredienten_add.php">Toevoegen</a>
                        </div>
                    </li>
                    
                    <li><a href="gebruikers_index.php">Beheer Gebruikers</a></li>
                <?php } ?>
                <li class="dropdown">
                    <button class="dropdown_button">
                        <?php
                        if (isset($_SESSION['gebruikersnaam'])) {
                        ?>
                            <a href="#"><?php echo $_SESSION['gebruikersnaam']; ?></a>
                        <?php
                        } else {
                        ?>
                            <a href="login.php">Inloggen</a>
                        <?php   
                        }
                        ?>
                    </button>
                    <ul class="dropdown-content">
                        <li><a href="bezoeker_detail.php">Mijn gegevens</a></li>
                        <li><a href="favorieten_index.php">Favorieten</a></li>
                        <li>
                            <div class="font-size-buttons">
                                <button id="small-text">Klein</button>
                                <button id="medium-text">Middel</button>
                                <button id="large-text">Groot</button>
                            </div>
                        </li>
                        <?php
                        if (isset($_SESSION['gebruikersnaam'])) {
                        ?>
                            <li><a href="loguit.php">Uitloggen</a></li>
                        <?php
                        } else {
                        ?>
                            <li><button id="login_button" onclick="window.location.href = 'login.php';">Login</button></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <script src="path/to/script.js"></script>
</header>

