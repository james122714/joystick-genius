
<?php
include 'conexion.php';
$query = "SELECT * FROM noticias WHERE destacada = 1 ORDER BY fecha DESC LIMIT 5";
$result = mysqli_query($conexion, $query);

// Display highlighted news
echo "<div class='destacadas'>";
echo "<h2>Noticias Destacadas</h2>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<div class='noticia'>";
    echo "<h3>" . $row['titulo'] . "</h3>";
    echo "<p>" . substr($row['contenido'], 0, 100) . "...</p>";
    echo "<a href='noticia.php?id=" . $row['id'] . "'>Leer más</a>";
    echo "</div>";
}
echo "</div>";

// Display other news
$query = "SELECT * FROM noticias WHERE destacada = 0 ORDER BY fecha DESC";
$result = mysqli_query($conexion, $query);
echo "<div class='otras'>";
echo "<h2>Otras Noticias</h2>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<div class='noticia'>";
    echo "<h3>" . $row['titulo'] . "</h3>";
    echo "<p>" . substr($row['contenido'], 0, 100) . "...</p>";
    echo "<a href='noticia.php?id=" . $row['id'] . "'>Leer más</a>";
    echo "</div>";
}
echo "</div>";
?>
