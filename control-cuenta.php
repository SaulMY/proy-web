//clase para manejar operaciones con la base de datos.
<?php 
class sql{
    public $conn;
    
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'BudGet';
        $username = 'root';
        $password = '';

        $this->conn=new mysqli($host, $username, $password, $dbname);
    }
        
    public function sumaIngresosUsuario($user_id){
        $sql = "SELECT SUM(monto) AS total_ingresos
                FROM movimientos 
                WHERE user_id = ? AND tipo = 'ingreso'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_ingresos'] ?? 0;
    }

    public function sumaGastosUsuario($user_id){
        $sql = "SELECT SUM(monto) AS total_gastos 
                FROM movimientos 
                WHERE user_id = ? AND tipo = 'gasto'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_gastos'] ?? 0; // Devuelve 0 si no hay gastos
    }

    public function balanceUsuario($user_id){
        $ingresos = $this->sumaIngresosUsuario($user_id);
        $gastos = $this->sumaGastosUsuario($user_id);
        return $ingresos - $gastos;
    }

    

}

?>