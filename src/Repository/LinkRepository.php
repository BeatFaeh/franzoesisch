<?php
declare(strict_types=1);

final class LinkRepository
{
    public function __construct(private mysqli $db) {}

    public function all(string $search = ''): array
    {
        $search = trim($search);
        if ($search === '') {
            $result = $this->db->query('SELECT id, titel, url, beschreibung FROM französisch_links ORDER BY titel ASC, id ASC');
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }
        $sql = 'SELECT id, titel, url, beschreibung FROM französisch_links WHERE titel LIKE ? OR url LIKE ? OR beschreibung LIKE ?';
        $types = 'sss';
        $params = [];
        $like = '%' . $search . '%';
        $params[] = &$like; $params[] = &$like; $params[] = &$like;
        if (ctype_digit($search)) {
            $sql .= ' OR id = ?';
            $types .= 'i';
            $id = (int)$search;
            $params[] = &$id;
        }
        $sql .= ' ORDER BY titel ASC, id ASC';
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    public function add(string $titel, string $url, string $beschreibung): bool
    {
        $stmt = $this->db->prepare('INSERT INTO französisch_links (titel, url, beschreibung) VALUES (?, ?, ?)');
        if (!$stmt) return false;
        $stmt->bind_param('sss', $titel, $url, $beschreibung);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function update(int $id, string $titel, string $url, string $beschreibung): bool
    {
        $stmt = $this->db->prepare('UPDATE französisch_links SET titel = ?, url = ?, beschreibung = ? WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('sssi', $titel, $url, $beschreibung, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM französisch_links WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
