<?php
require_once 'BaseModel.php';

class Station extends BaseModel {
    protected $table = 'stations';
    
    public function authenticate($email, $password) {
        $station = $this->findBy('email', $email);
        
        if ($station && password_verify($password, $station['password'])) {
            return $station;
        }
        
        return false;
    }
    
    public function register($data) {
        // Validation des données
        if (!$this->validateStationData($data)) {
            return false;
        }
        
        // Vérifier si l'email existe déjà
        if ($this->findBy('email', $data['email'])) {
            throw new Exception('Cet email est déjà utilisé');
        }
        
        // Géocoder l'adresse
        $coordinates = $this->geocodeAddress($data['adresse']);
        
        // Préparer les données
        $stationData = [
            'nom' => $data['nom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'stock_gaz' => intval($data['stock_gaz']),
            'prix_unite' => floatval($data['prix_unite']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($stationData);
    }
    
    public function searchNearby($userLat, $userLng, $quantite = 1) {
        // Utilise une constante PHP pour le rayon, avec une valeur par défaut pour la robustesse.
        $radius = defined('DELIVERY_RADIUS_KM') ? DELIVERY_RADIUS_KM : 25;
        $earthRadius = 6371; // Rayon de la Terre en km

        // 1. Calcul du "bounding box" pour pré-filtrer efficacement les stations.
        $latRad = deg2rad($userLat);
        
        $latDelta = rad2deg($radius / $earthRadius);
        $lngDelta = rad2deg($radius / ($earthRadius * cos($latRad)));

        $minLat = $userLat - $latDelta;
        $maxLat = $userLat + $latDelta;
        $minLng = $userLng - $lngDelta;
        $maxLng = $userLng + $lngDelta;

        // 2. Requête SQL simplifiée et compatible avec toutes les BDD (SQLite, MySQL, etc.)
        $stmt = $this->db->prepare("
            SELECT *
            FROM stations 
            WHERE stock_gaz >= ? 
              AND active = 1
              AND (latitude BETWEEN ? AND ?)
              AND (longitude BETWEEN ? AND ?)
        ");
        
        $stmt->execute([$quantite, $minLat, $maxLat, $minLng, $maxLng]);
        $stationsInBox = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Calcul précis de la distance et filtrage final en PHP
        $stations = [];
        foreach ($stationsInBox as $station) {
            $lat1 = deg2rad($userLat);
            $lng1 = deg2rad($userLng);
            $lat2 = deg2rad($station['latitude']);
            $lng2 = deg2rad($station['longitude']);

            $a = sin(($lat2 - $lat1) / 2)**2 + cos($lat1) * cos($lat2) * sin(($lng2 - $lng1) / 2)**2;
            $distance = $earthRadius * 2 * asin(sqrt($a));

            if ($distance <= $radius) {
                $station['distance_km'] = $distance;
                $stations[] = $station;
            }
        }
        
        // 4. Tri par distance et limitation des résultats
        usort($stations, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        $stations = array_slice($stations, 0, 15);

        foreach ($stations as &$station) {
            $station['distance_km'] = floatval($station['distance_km']);
            $station['latitude'] = floatval($station['latitude']);
            $station['longitude'] = floatval($station['longitude']);
            $station['prix_unite'] = floatval($station['prix_unite']);
            $station['stock_gaz'] = intval($station['stock_gaz']);
        }
        
        return $stations;
    }
    
    public function getOrders($stationId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.nom as client_nom, u.telephone as client_telephone 
            FROM commandes c 
            JOIN utilisateurs u ON c.utilisateur_id = u.id 
            WHERE c.station_id = ? 
            ORDER BY c.created_at DESC
        ");
        
        $stmt->execute([$stationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStats($stationId) {
        $stmt = $this->db->prepare("
            SELECT 
                stock_gaz,
                prix_unite,
                (SELECT COUNT(*) FROM commandes WHERE station_id = ? AND statut = 'en_attente') as commandes_attente,
                (SELECT COUNT(*) FROM commandes WHERE station_id = ? AND DATE(created_at) = date('now')) as commandes_aujourd_hui,
                (SELECT SUM(prix_total) FROM commandes WHERE station_id = ? AND statut = 'livree' AND DATE(created_at) = date('now')) as revenus_jour
            FROM stations 
            WHERE id = ?
        ");
        
        $stmt->execute([$stationId, $stationId, $stationId, $stationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function validateStationData($data) {
        $required = ['nom', 'email', 'telephone', 'adresse', 'stock_gaz', 'prix_unite', 'password'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Le champ {$field} est requis");
            }
        }
        
        // Validation email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email invalide');
        }
        
        // Validation téléphone camerounais
        if (!preg_match('/^(6[5-9]|2[2-3])[0-9]{7}$/', $data['telephone'])) {
            throw new Exception('Numéro de téléphone camerounais invalide');
        }
        
        // Validation stock
        if (!is_numeric($data['stock_gaz']) || $data['stock_gaz'] < 0) {
            throw new Exception('Stock invalide');
        }
        
        // Validation prix
        if (!is_numeric($data['prix_unite']) || $data['prix_unite'] < 500) {
            throw new Exception('Prix invalide (minimum 500 FCFA)');
        }
        
        return true;
    }
    
    private function geocodeAddress($address) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address . ", Cameroun"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "GazCameroun/1.0 (contact@gazcameroun.cm)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $geoData = curl_exec($ch);
        curl_close($ch);
        
        $geoResult = json_decode($geoData, true);
        
        if (!empty($geoResult)) {
            return [
                'lat' => floatval($geoResult[0]['lat']),
                'lng' => floatval($geoResult[0]['lon'])
            ];
        }
        
        return ['lat' => DEFAULT_LAT, 'lng' => DEFAULT_LNG];
    }
}
?>