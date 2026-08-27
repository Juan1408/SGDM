<?php
$hash = '$2y$10$e8wYVjFw6rNKGZqfD7h.TeuXG8o0i1bH2s8pQ.b7zG4vYwR6M9Veq';
if (password_verify('123456', $hash)) echo "Match 123456!"; 
if (password_verify('admin123', $hash)) echo "Match admin123!"; 
if (password_verify('Admin123', $hash)) echo "Match Admin123!"; 
