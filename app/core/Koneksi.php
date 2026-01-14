<?php // app/core/Koneksi.php

class Koneksi
{
    private $koneksi;
    private $stmt;

    public function __construct()
    {
        // Menggunakan konstanta dari Config.php
        $this->koneksi = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if ($this->koneksi->connect_error) {
            error_log("Connection failed: " . $this->koneksi->connect_error);
            die("Terjadi masalah saat menyambungkan ke database. Silakan coba lagi nanti.");
        }
    }

    public function getKoneksi()
    {
        return $this->koneksi;
    }

    public function closeKoneksi()
    {
        if ($this->stmt) {
            $this->stmt->close();
            $this->stmt = null; 
        }
        if ($this->koneksi) {
            $this->koneksi->close();
            $this->koneksi = null; 
        }
    }

    public function query($sql)
    {
        $result = $this->koneksi->query($sql);
        if (!$result) {
            error_log("Query failed: " . $this->koneksi->error . " (SQL: " . $sql . ")");
            die("Terjadi kesalahan pada database.");
        }
        return $result;
    }

    public function prepare($query)
    {
        $this->stmt = $this->koneksi->prepare($query);
        if (!$this->stmt) {
            error_log("Prepare failed: " . $this->koneksi->error . " (Query: " . $query . ")");
            die("Terjadi kesalahan saat mempersiapkan statement database.");
        }
    }

    // Bind parameter untuk prepared statement
    public function bind($types, ...$params)
    {
        if (!$this->stmt) {
            die("Error: Statement belum dipersiapkan untuk bind parameter.");
        }
        if (!empty($types) && count($params) > 0) { 
            // Menggunakan call_user_func_array untuk bind_param dengan variadic parameters
            // Ini adalah cara yang robust untuk menangani referensi yang dibutuhkan oleh bind_param
            $bind_names = [$types]; 
            foreach ($params as $key => &$param) { // Perhatikan '&' untuk referensi
                $bind_names[] = &$param;
            }
            call_user_func_array([$this->stmt, 'bind_param'], $bind_names);
        }
    }

    public function execute()
    {
        if (!$this->stmt) {
            die("Error: Statement belum dipersiapkan untuk dieksekusi.");
        }
        if (!$this->stmt->execute()) {
            error_log("Execute failed: " . $this->stmt->error);
            die("Terjadi kesalahan saat mengeksekusi statement database.");
        }
        return $this->stmt->get_result();
    }

    public function resultset()
    {
        $result = $this->execute();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function single()
    {
        $result = $this->execute();
        return $result ? $result->fetch_assoc() : null;
    }
    public function rowCount()
    {
        return $this->stmt->affected_rows;
    }
} // TIDAK ADA KURUNG KURAWAL SETELAH INI
