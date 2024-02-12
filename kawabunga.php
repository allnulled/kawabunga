<?php

define("PHP_ENV", "test");
define("MYSQL_HOST", "127.0.0.1");
define("MYSQL_PORT", "3306");
define("MYSQL_USER", "root");
define("MYSQL_PASSWORD", "");
// define("MYSQL_DATABASE", "example");
define("MYSQL_DATABASE", "ejemplo");
define("FRAMEWORK_NAME", "kawabunga");
define("FRAMEWORK_VERSION", "0.0.1");

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Request
{
    public $framework;
    private $mixed_parameters;
    public $authentication;
    public function __construct($framework)
    {
        $this->framework = $framework;
        $this->mixed_parameters = array_merge($_GET, $_POST);
    }
    public function get_all_parameters()
    {
        return $this->mixed_parameters;
    }
    public function get_parameter($name, $default_value = null)
    {
        return isset($this->mixed_parameters[$name]) ? $this->mixed_parameters[$name] : $default_value;
    }
    public function require_parameter($name)
    {
        if(!isset($this->mixed_parameters[$name])) {
            throw new Exception("Required parameter «{$name}»");
        }
        return $this->mixed_parameters[$name];
    }
    public function parse_parameter_as_array($name, $default_value = null)
    {
        $parameter = null;
        if(!isset($this->mixed_parameters[$name])) {
            $parameter = $default_value;
        } else {
            $parameter = $this->mixed_parameters[$name];
        }
        if(is_string($parameter)) {
            $parameter = json_decode($parameter, true);
            if ($parameter === null && json_last_error() !== JSON_ERROR_NONE) {
                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $error_message = 'Se alcanzó la profundidad máxima del stack';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $error_message = 'JSON malformado o inválido';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $error_message = 'Error de control de caracteres, posiblemente incorrecto codificado';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $error_message = 'Error de sintaxis, JSON mal formado';
                        break;
                    case JSON_ERROR_UTF8:
                        $error_message = 'Problema con caracteres UTF-8, posiblemente malformados';
                        break;
                    default:
                        $error_message = 'Error desconocido al decodificar JSON';
                        break;
                }
                throw new Exception($error_message);
            }
        }
        return $parameter;
    }
    public function parse_parameter_as_number($name, $default_value = null)
    {
        $parameter = null;
        if(!isset($this->mixed_parameters[$name])) {
            $parameter = $default_value;
        } else {
            $parameter = $this->mixed_parameters[$name];
        }
        if(is_string($parameter)) {
            $parameter = json_decode($parameter, true);
        }
        if(!is_int($parameter)) {
            $parameter = intval($parameter);
        }
        return $parameter;
    }
}

class Database
{
    public $framework;
    public $connection;
    public function __construct($framework)
    {
        $this->framework = $framework;
        $dsn = "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE;
        try {
            $this->connection = new PDO($dsn, MYSQL_USER, MYSQL_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $this->framework->dispatch_error(array("message" => "Connection failed: " . $e->getMessage()));
            exit;
        }
    }
    public function query($query, $parameters)
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($parameters);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select($table, $where, $sort, $page)
    {
        $sql = "SELECT * FROM $table";
        if (!empty($where)) {
            $whereClause = [];
            foreach ($where as $condition) {
                $whereClause[] = "{$condition[0]} {$condition[1]} ?";
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }
        if (!empty($sort)) {
            $orderByClause = [];
            foreach ($sort as $sortColumn) {
                $orderByClause[] = "$sortColumn[0] $sortColumn[1]";
            }
            $sql .= " ORDER BY " . implode(", ", $orderByClause);
        }
        $page = intval($page);
        if ($page > 0) {
            $offset = ($page * 20) - 20;
            $sql .= " LIMIT 20 OFFSET $offset";
        }
        $statement = $this->connection->prepare($sql);
        $values = array_column($where ?? [], 2);
        $statement->execute($values);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function insert($table, $value)
    {
        $columns = implode(', ', array_keys($value));
        $columnValues = implode(', ', array_fill(0, count($value), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($columnValues)";
        $statement = $this->connection->prepare($sql);
        $statement->execute(array_values($value));
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $id, $value)
    {
        $setClause = [];
        foreach ($value as $column => $val) {
            $setClause[] = "$column = ?";
        }
        $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE id = ?";
        $values = array_merge(array_values($value), [$id]);
        $statement = $this->connection->prepare($sql);
        $statement->execute($values);
        return $statement->rowCount();
    }
    
    public function delete($table, $id)
    {
        $sql = "DELETE FROM $table WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        return $statement->rowCount();
    }
    
}

class Auth {
    public $framework;
    public function __construct($framework)
    {
        $this->framework = $framework;
    }
    public function dispatch() {
        $operation = $this->framework->request->get_parameter("operation", null);
        switch($operation) {
            case "register_account":
                $name = $this->framework->request->get_parameter("name", null);
                $password = $this->framework->request->get_parameter("password", null);
                $email = $this->framework->request->get_parameter("email", null);
                $this->register_account($name, $password, $email);
                break;
            case "confirm_account":
                $email = $this->framework->request->get_parameter("email", null);
                $confirmation_token = $this->framework->request->get_parameter("confirmation_token", null);
                $this->confirm_account($email, $confirmation_token);
                break;
            case "login_session":
                $email = $this->framework->request->get_parameter("email", null);
                $password = $this->framework->request->get_parameter("password", null);
                $this->login_session($email, $password);
                break;
            case "refresh_session":
                $token = $this->framework->request->get_parameter("token", null);
                $this->refresh_session($token);
                break;
            case "logout_session":
                $token = $this->framework->request->get_parameter("token", null);
                $this->logout_session($token);
                break;
            case "forgot_credentials":
                $email = $this->framework->request->get_parameter("email", null);
                $this->forgot_credentials($email);
                break;
            case "recover_credentials":
                $email = $this->framework->request->get_parameter("email", null);
                $recovery_token = $this->framework->request->get_parameter("recovery_token", null);
                $this->recover_credentials($email, $recovery_token);
                break;
            case "change_password":
                $email = $this->framework->request->get_parameter("email", null);
                $password = $this->framework->request->get_parameter("password", null);
                $password_confirmation = $this->framework->request->get_parameter("password_confirmation", null);
                $this->change_password($email, $password, $password_confirmation);
                break;
            case "unregister_account":
                $email = $this->framework->request->get_parameter("email", null);
                $password = $this->framework->request->get_parameter("password", null);
                $this->unregister_account($email, $password);
                break;
        }
    }
    public function authenticate() {
        $authentication = $this->framework->request->get_parameter("authentication");
        if (empty($authentication)) {
            return false;
        }
        $data = $this->framework->database->query("SELECT"
        . "  kw_session.id AS 'kw_session.id'," 
        . "  kw_session.id_kw_user AS 'kw_session.id_kw_user'," 
        . "  kw_session.token AS 'kw_session.token'," 
        . "  kw_group.id AS 'kw_group.id'," 
        . "  kw_group.name AS 'kw_group.name'," 
        . "  kw_group.description AS 'kw_group.description'," 
        . "  kw_permission.id AS 'kw_permission.id'," 
        . "  kw_permission.name AS 'kw_permission.name'," 
        . "  kw_permission.description AS 'kw_permission.description'" 
        . " FROM kw_session"
        . " LEFT JOIN kw_user ON kw_session.id_kw_user = kw_user.id"
        . " LEFT JOIN kw_user_and_kw_group ON kw_user_and_kw_group.id_kw_user = kw_user.id"
        . " LEFT JOIN kw_group ON kw_group.id = kw_user_and_kw_group.id_kw_group"
        . " LEFT JOIN kw_group_and_kw_permission ON kw_group_and_kw_permission.id_kw_group = kw_group.id"
        . " LEFT JOIN kw_permission ON kw_group_and_kw_permission.id_kw_permission = kw_permission.id"
        . " WHERE kw_session.token = ?", [$authentication]);
        $this->framework->request->authentication = $data;
    }
    public function register_account($name, $password, $email) {
        // @TODO
    }
    public function confirm_account($email, $confirmation_token) {
        // @TODO
    }
    public function login_session($email, $password) {
        // @TODO
    }
    public function refresh_session($token) {
        // @TODO
    }
    public function logout_session($token) {
        // @TODO
    }
    public function forgot_credentials($email) {
        // @TODO
    }
    public function recover_credentials($email, $recovery_token) {
        // @TODO
    }
    public function change_password($email, $password, $password_confirmation) {
        // @TODO
    }
    public function unregister_account($email, $password) {
        // @TODO
    }
}

class Utilities
{
    public $framework;
    public function __construct($framework)
    {
        $this->framework = $framework;
    }
}

class Framework
{
    public $name = FRAMEWORK_NAME;
    public $version = FRAMEWORK_VERSION;
    public $utilities;
    public $database;
    public $request;
    public $auth;
    public function __construct()
    {
        $this->utilities = new Utilities($this);
        $this->database = new Database($this);
        $this->request = new Request($this);
        $this->auth = new Auth($this);
    }
    public function dispatch_success($data)
    {
        echo json_encode(array_merge([
            "status" => "success",
            "success" => true
        ], $data), JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
    }
    public function dispatch_error($data)
    {
        echo json_encode(array_merge([
            "status" => "error",
            "error" => true
        ], $data), JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
    }
    public function dispatch()
    {
        try {
            $this->auth->authenticate();
            $operation = $this->request->get_parameter("operation", null);
            if (!isset($operation)) {
                $this->dispatch_error([
                    "message" => "Required parameter «operation»"
                ]);
                exit;
            }
            $is_rest_operation = in_array($operation, ["select", "insert", "update", "delete"]);
            $is_auth_operation = in_array($operation, ["register", "confirm", "login", "refresh", "logout", "forgot_password", "recover_password", "unregister"]);
            if ($is_auth_operation) {
                $this->auth->dispatch();
                exit;
            }
            if (!$is_rest_operation) {
                $this->dispatch_error([
                    "message" => "Required parameter «operation» to be valid"
                ]);
                exit;
            }
            $table = $this->request->get_parameter("table", null);
            if (!isset($table)) {
                $this->dispatch_error([
                    "message" => "Required parameter «table»"
                ]);
                exit;
            }
            $result = null;
            switch($operation) {
                case "select": 
                    $where = $this->request->parse_parameter_as_array("where", []);
                    $sort = $this->request->parse_parameter_as_array("sort", []);
                    $page = $this->request->parse_parameter_as_number("page", 1);
                    if(!is_array($where)) {
                        throw new Exception("Required valid parameter «where»");
                    }
                    if(!is_array($sort)) {
                        throw new Exception("Required valid parameter «sort»");
                    }
                    if(!is_integer($page)) {
                        throw new Exception("Required valid parameter «page»");
                    }
                    $result = $this->database->select($table, $where, $sort, $page);
                    break;
                case "insert":
                    $value = $this->request->parse_parameter_as_array("value", []);
                    if(!is_array($value)) {
                        throw new Exception("Required valid parameter «value»");
                    }
                    $result = $this->database->insert($table, $value);
                    break;
                case "update":
                    $id = $this->request->parse_parameter_as_number("id", 0);
                    $value = $this->request->parse_parameter_as_array("value", []);
                    if(!is_integer($id)) {
                        throw new Exception("Required valid parameter «id»");
                    }
                    if(!is_array($value)) {
                        throw new Exception("Required valid parameter «value»");
                    }
                    $result = $this->database->update($table, $id, $value);
                    break;
                case "delete": 
                    $id = $this->request->parse_parameter_as_number("id", 0);
                    if(!is_integer($id)) {
                        throw new Exception("Required valid parameter «id»");
                    }
                    $result = $this->database->delete($table, $id);
                    break;
                default:
                    throw new Exception("Required parameter «operation» to be valid");
            }
            return $this->dispatch_success([ "response" => $result ]);
        } catch (Exception $ex) {
            return $this->dispatch_error([
                "message" => $ex->getMessage()
            ]);
        }
    }
}
$framework = new Framework();
function get_kawabunga()
{
    global $framework;
    return $framework;
}
?>