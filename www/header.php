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
    <nav>
        <div>
            <div>Recepten</a></div>
            <ul>
                <li><a href="#">Mijn Verzameling</a></li>
                <li><a href="#">Zeldzame Pokémon</a></li>
                <?php if ($rol === 'beheerder') { ?>
                    <!-- Dropdown Gebruikers -->
                    <li class="dropdown">
                        <a href="#">Recepten</a>
                        <div class="dropdown-content">
                            <a href="recepten_index.php">Bekijken</a>
                            <a href="recepten_add.php">Toevoegen</a>
                        </div>
                    </li>

                    <!-- Dropdown Kaarten -->
                    <li class="dropdown">
                        <a href="#">Kaarten</a>
                        <div class="dropdown-content">
                            <a href="pokemon_index.php">Bekijken</a>
                            <a href="pokemon_add.php">Toevoegen</a>
                        </div>
                    </li>

                    <!-- Dropdown Types -->
                    <li class="dropdown">
                        <a href="#">Types</a>
                        <div class="dropdown-content">
                            <a href="types_index.php">Bekijken</a>
                            <a href="types_add.php">Toevoegen</a>
                        </div>
                    </li>
                <?php } ?>
                <li><a href="#">Over Ons</a></li>
                <li><a href="#">Contact</a></li>
                <div class="dropdown">
                    <button class="dropdown_button">
                        <?php
                        if(isset($_SESSION['gebruikersnaam'])){
                        ?>
                        <a href="#"><?php echo $_SESSION['gebruikersnaam'] ?></a>
                        <?php
                        } else {
                        ?>
                        <a href="login.php">Inloggen</a>
                        <?php   
                        }
                        ?>
                    </button>
                    <ul class="dropdown-content">
                        <li><a href="#">Mijn gegevens</a></li>
                        <?php
                        if(isset($_SESSION['gebruikersnaam'])){
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
                </div>
            </ul>
        </div>
    </nav>
</header>