<?php // app/models/Auth_model.php

class Auth_model
{
    private $db;
    private $tabel = 'user';

    public function __construct()
    {
        $this->db = new Koneksi();
    }

    public function getUserByEmail($email)
    {
        $this->db->prepare('SELECT ID_User, username, full_name, email, pass FROM ' . $this->tabel . ' WHERE email = ?');
        $this->db->bind('s', $email);
        return $this->db->single();
    }

    public function getUserByUsername($username)
    {
        $this->db->prepare('SELECT ID_User FROM ' . $this->tabel . ' WHERE username = ?');
        $this->db->bind('s', $username);
        return $this->db->single();
    }

    public function createUser($data)
    {
        $query = 'INSERT INTO ' . $this->tabel . ' (username, full_name, email, pass, alamat, no_telepon) VALUES (?, ?, ?, ?, ?, ?)';
        $this->db->prepare($query);

        $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);

        // Ubah tipe binding untuk no_telepon dari 'i' menjadi 's' (string)
        // Binding: username, full_name, email, hashedPassword, alamat, no_telepon
        // Tipe:    s,      s,         s,     s,              s,      s 
        $this->db->bind('ssssss',  // Sebelumnya 'sssssi'
            $data['username'], 
            $data['full_name'], 
            $data['email'], 
            $hashedPassword,
            $data['alamat'],
            $data['no_telepon'] // Akan dikirim sebagai string
        );

        $this->db->execute();

        return $this->db->rowCount() > 0;
    }

    public function getUserById($id)
    {
        $this->db->prepare('SELECT ID_User, username, full_name, email, alamat, no_telepon FROM ' . $this->tabel . ' WHERE ID_User = ?');
        $this->db->bind('i', $id);
        return $this->db->single();
    }
}