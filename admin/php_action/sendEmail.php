<?php
include "db_connect.php";
require('fpdf.php');

$id = $_GET['id'];

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    // $this->Image('logo.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(60);
    // Title
    $this->Cell(80,10,'Pembelian Tiket - Java Jazz',1,0,'C');
    // Line break
    $this->Ln(20);
}

// Page footer
function Footer(){
      // Position at 1.5 cm from bottom
      $this->SetY(-15);
      // Arial italic 8
      $this->SetFont('Arial','I',8);
      // Page number
      $this->Cell(0,10,'Copyright - Tukang Koding ',0,0,'C');
  }
}

$sql = "SELECT booking.id_booking, booking.nama_pembeli, customers.username, customers.email, tickets.jenis_tiket, tickets.harga
FROM booking
INNER JOIN customers ON booking.id_customer = customers.id_customer
INNER JOIN tickets ON booking.id_ticket = tickets.id_ticket
WHERE booking.status = 1 AND booking.id_booking = $id";

$result = $connect->query($sql);
while ($row = $result->fetch_array()) {
  $id_booking = $row[0];
  $nama_pembeli = $row[1];
  $username = $row[2];
  $email = $row[3];
  $jenis_tiket = $row[4];
  $harga = $row[5];
  $qr_code = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$row[0].'.png";

  $pdf = new PDF();
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->SetFont('Times','',14);

  $pdf->Cell(1,10,'ID Booking              : '.$id_booking,0,1,'L');
  $pdf->Cell(40,10,'Nama Pembeli         : '.$nama_pembeli,0,1,'L');
  $pdf->Cell(40,10,'Username                : '.$username,0,1,'L');
  $pdf->Cell(40,10,'Jenis Tiket               : '.$jenis_tiket,0,1,'L');
  $pdf->Cell(40,10,'Email                       : '.$email,0,1,'L');
  $pdf->Cell(40,10,'Harga                       : '.$harga,0,1,'L');
  $pdf->Image($qr_code,120,90,60);
  $pdf->Output();



  require '../plugin/PHPMailer/Exception.php';
  require '../plugin/PHPMailer/PHPMailer.php';
  require '../plugin/PHPMailer/SMTP.php';
  require '../plugin/PHPMailer/POP3.php';
  require '../plugin/PHPMailer/OAuth.php';

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
  try {
      //Server settings
      $sender = "wahyuemailaja@gmail.com"
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = $sender;                 // SMTP username
      $mail->Password = 'dragonnest';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;                                    // TCP port to connect to

      //Recipients
      $mail->setFrom($sender, 'Mailer');
      $mail->addAddress($email);               // Name is optional

      //Attachments
      $mail->addAttachment('../../images/ticket.png', 'test.png');    // Optional name

      //Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = 'Here is the subject';
      $mail->Body    = 'This is the HTML message body <b>in bold!</b>';

      $mail->send();
      echo 'Message has been sent';
  } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  }

}


// Instanciation of inherited class

?>
