<?php

// Fonction qui simplifie l'utilisation de htmlentities
function e(string $string) {
    return htmlentities($string);
}