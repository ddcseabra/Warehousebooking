# ใช้ base image ที่มี PHP และ Apache web server ติดตั้งมาให้แล้ว
FROM php:8.2-apache

# Copy โค้ดทั้งหมดในโปรเจกต์ปัจจุบัน ไปยังโฟลเดอร์ของเว็บเซิร์ฟเวอร์ใน container
COPY . /var/www/html/
