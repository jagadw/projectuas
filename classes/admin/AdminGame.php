<?php
class AdminGame extends Admin
{
    private $platforms = ['PC', 'Multiplatform', 'Playstation', 'Xbox'];

    public function getPlatforms()
    {
        return $this->platforms;
    }

    public function getGame($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getSelectedGenres($gameId)
    {
        $stmt = $this->conn->prepare("SELECT genre_id FROM game_genres WHERE game_id = ?");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $result = $stmt->get_result();

        $genres = [];
        while($row = $result->fetch_assoc()) {
            $genres[] = $row['genre_id'];
        }
        return $genres;
    }

    public function getGenres()
    {
        $stmt = $this->conn->prepare("SELECT * FROM genres ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getGames()
    {
        $stmt = $this->conn->prepare("
            SELECT g.*, GROUP_CONCAT(ge.name ORDER BY ge.name SEPARATOR ', ') genres
            FROM games g
            LEFT JOIN game_genres gg ON gg.game_id = g.id
            LEFT JOIN genres ge ON ge.id = gg.genre_id
            GROUP BY g.id
            ORDER BY g.created_at DESC, g.id DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function saveGame($data, $file)
    {
        $id = (int) ($data['id'] ?? 0);
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = (float) ($data['price'] ?? 0);
        $platform = in_array($data['platform'] ?? '', $this->platforms, true) ? $data['platform'] : 'PC';
        $oldImage = trim($data['old_image'] ?? '');
        $image = $this->uploadImage($file, $oldImage);
        $genreIds = array_map('intval', $data['genres'] ?? []);

        if ($title === '') {
            return false;
        }

        if ($id > 0) {
            $stmt = $this->conn->prepare("UPDATE games SET title = ?, description = ?, price = ?, platform = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdssi", $title, $description, $price, $platform, $image, $id);
            $stmt->execute();
            $gameId = $id;
        } else {
            $stmt = $this->conn->prepare("INSERT INTO games (title, description, price, platform, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $title, $description, $price, $platform, $image);
            $stmt->execute();
            $gameId = $this->conn->insert_id;
        }

        $this->syncGenres($gameId, $genreIds);
        return true;
    }

    private function syncGenres($gameId, $genreIds)
    {
        $stmt = $this->conn->prepare("DELETE FROM game_genres WHERE game_id = ?");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();

        foreach (array_unique($genreIds) as $genreId) {
            if ($genreId > 0) {
                $stmt = $this->conn->prepare("INSERT INTO game_genres (game_id, genre_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $gameId, $genreId);
                $stmt->execute();
            }
        }
    }

    public function deleteGame($id)
    {
        if ($id > 0) {
            $stmt = $this->conn->prepare("DELETE FROM games WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
        return false;
    }

    public function addGenre($name)
    {
        if ($name !== '') {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO genres (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            return $stmt->execute();
        }
        return false;
    }

    private function uploadImage($file, $oldImage)
    {
        $defaultImage = 'palworld.jpg';

        if (!isset($file['image']) || $file['image']['error'] !== UPLOAD_ERR_OK) {
            return $oldImage !== '' ? $oldImage : $defaultImage;
        }

        $tmpPath = $file['image']['tmp_name'];

        $maxSizeBytes = 3 * 1024 * 1024;
        if (!isset($file['image']['size']) || (int)$file['image']['size'] <= 0 || (int)$file['image']['size'] > $maxSizeBytes) {
            return $oldImage !== '' ? $oldImage : $defaultImage;
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
        ];

        $mime = '';
        if (function_exists('mime_content_type')) {
            $mime = (string)mime_content_type($tmpPath);
        }

        if (!isset($allowedTypes[$mime]) && function_exists('getimagesize')) {
            $imgInfo = @getimagesize($tmpPath);
            if (is_array($imgInfo) && isset($imgInfo['mime']) && isset($allowedTypes[$imgInfo['mime']])) {
                $mime = (string)$imgInfo['mime'];
            }
        }

        if (!isset($allowedTypes[$mime])) {
            return $oldImage !== '' ? $oldImage : $defaultImage;
        }

        $ext = $allowedTypes[$mime];
        $filename = 'game_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $assetsDir = dirname(__DIR__, 2) . '/public/uploads/';
        $target = $assetsDir . $filename;

        if (!is_dir($assetsDir)) {
            @mkdir($assetsDir, 0775, true);
        }

        if (move_uploaded_file($tmpPath, $target)) {
            return $filename;
        }

        return $oldImage !== '' ? $oldImage : $defaultImage;

    }
}
?>
