<?php

class Borrow extends Model
{
    protected $table = "borrowed_books"; // Table for storing borrow records

    public function borrowBook($user_id, $book_id)
    {
        $due_date = date("Y-m-d", strtotime("+14 days")); // 2 weeks from today

        $query = "INSERT INTO borrowed_books (user_id, book_id, borrowed_at, return_date, status) 
                  VALUES (:user_id, :book_id, NOW(), :return_date, 'borrowed')";

        return $this->db->query($query, $user_id, $book_id);
    }

    public function returnBook($borrow_id)
    {
        $query = "UPDATE borrowed_books SET status = 'returned', returned_at = NOW() WHERE id = :id";

        return $this->db->query($query,$borrow_id);
    }

    public function getUserBorrowedBooks($user_id)
    {
        $query = "SELECT b.id, bk.title, bk.author, b.return_date, b.status
                  FROM borrowed_books b
                  JOIN books bk ON b.book_id = bk.id
                  WHERE b.user_id = user_id";

        return $this->db->query($query,$user_id)->fetchAll();
    }

    public function checkOverdueBooks()
    {
        $query = "UPDATE borrowed_books 
                  SET status = 'overdue' 
                  WHERE status = 'borrowed' AND return_date < CURDATE()";

        return $this->db->query($query);
    }
}
