<?php

require_once __DIR__.'/vendor/autoload.php';

use SVG\Nodes\Shapes\SVGRect;
use SVG\Nodes\Texts\SVGText;
use SVG\SVG;

$db = new \PDO('sqlite:'.__DIR__.'/db.sqlite');
$db = new \SQLite3(__DIR__.'/db.sqlite');
$db->query(
	'CREATE TABLE IF NOT EXISTS "visits" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "referrer" TEXT,
    "time" DATETIME
)'
);

$statement = $db->prepare('INSERT INTO "visits" ("referrer", "time") VALUES (:url, :time)');
$statement->bindValue(':url', $_SERVER['HTTP_REFERER'] ?? null);
$statement->bindValue(':time', date('Y-m-d H:i:s'));
$statement->execute();

$result = $db->prepare('SELECT count(*) FROM "visits"')->execute();

$count = $result->fetchArray()[0];

$image = new SVG(200, 200);
$doc = $image->getDocument();

$square = new SVGRect(0, 0, $width = 200, 50);
$square->setAttribute('fill', '#123456');
$doc->addChild($square);

$textWidth = imagefontwidth($fontSize = 20) * strlen($count);
$text = new SVGText(number_format($count), ($width / 2) - ($textWidth / 2), 30);
$text->setSize($fontSize);
$text->setAttribute('fill', 'white');
$doc->addChild($text);

header("Cache-Control: no-cache, must-revalidate");
header('Content-Type: image/svg+xml');
echo $image;