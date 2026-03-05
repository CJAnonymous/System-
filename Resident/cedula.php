<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Indigency</title>
    <style>
        .sidebar a.active {
    background-color: #007bff; 
    color: white; 
    font-weight: bold; 
    border-radius: 5px;
    padding: 10px;
}

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            overflow-y: auto;
            width: 250px;
            background-color: #003566;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            transition: transform 0.3s ease;
        }

        .sidebar img {
            width: 100px;
            height: auto;
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #00214d;
        }

        .logout-btn {
            margin-top: auto;
            text-align: center;
            padding: 10px 15px;
            background-color: #DC3545;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        .logout-btn:hover {
            background-color: #a71d2a;
        }

        .main-content {
            overflow-y: auto;
            flex-grow: 1;
            padding: 30px 40px;
            margin-left: 250px;
            background-color: #f9fafb;
            transition: margin-left 0.3s ease;
        }

        .main-content h1 {
            font-size: 30px;
            color: #003566;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        form label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #003566;
        }

        form input, form select, form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        form input[type="file"] {
            padding: 5px;
            font-size: 14px;
        }

        form input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .note {
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        .hamburger {
            display: none;
            cursor: pointer;
            position: fixed;
            top: 15px;
            left: 20px;
            z-index: 3;
        }

        .hamburger div {
            width: 30px;
            height: 3px;
            background-color: #003566;
            margin: 5px;
            transition: 0.3s;
        }

        .hamburger.open div {
            background-color: white; 
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
        }

        @media (min-width: 769px) {
            .form-container {
                background-color: #fff;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
        }

        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.5); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 500px;
            border-radius: 10px;
            text-align: center;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
        }

        .payment-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .payment-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .payment-buttons button#cash-btn {
            background-color: #28a745;
            color: white;
        }

        .payment-buttons button#cash-btn:hover {
            background-color: #218838;
        }

        .payment-buttons button#gcash-btn {
            background-color: #17a2b8;
            color: white;
        }

        .payment-buttons button#gcash-btn:hover {
            background-color: #138496;
        }

        #gcash-qr {
            width: 200px;
            margin-top: 20px;
        }

        .ref-number {
            font-weight: bold;
            color: #007BFF;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        .close-button {
            float: right;
            font-size: 20px;
            color: #aaa;
            cursor: pointer;
        }

        .close-button:hover {
            color: black;
        }

        .modal h3 {
            color: #003566;
            margin-bottom: 15px;
        }

        .modal p {
            margin: 10px 0;
        }

        .modal .confirm-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .modal .confirm-btn:hover {
            background-color: #218838;
        }

        .modal .cancel-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .modal .cancel-btn:hover {
            background-color: #c82333;
        }
    .submit-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="hamburger" onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <?php include 'sidebar.php'; ?>

    <div class="main-content" onclick="closeSidebar()">
    <h1>Request of Cedula</h1>
    <div class="form-container">
        <!-- Add Requirements Note -->
        <div class="requirements-note" style="
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        ">
            <h3 style="color: #856404; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i> Important Requirements Note
            </h3>
            <p style="color: #856404; margin-bottom: 15px;">
                Please go to the Barangay Hall and bring these requirements:
            </p>
            <ul style="list-style-type: none; padding-left: 20px;">
                <li style="margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                    Accomplished Community Tax Declaration Form (CTDF) from City Treasurer's Office
                </li>
                <li style="margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                    Valid government-issued ID
                </li>
                <li style="margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                    Proof of Income
                </li>
                <li style="margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                    Copy of Payslips
                </li>
                <li style="margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                    BIR form 2316 or the Certificate of Compensation Payment or Income Tax Withheld
                </li>
                <li style="margin-bottom: 10px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                    For authorized representative: Valid government-issued ID of the representative
                </li>
            </ul>
        </div>
        <div style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 5px;">
        <p style="color: #155724; margin: 0;">
            <i class="fas fa-money-bill-wave"></i> 
            <strong>Payment Required:</strong> A fee of ₱60.00 must be paid upon submission
        </p>
    </div>
    <div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closePaymentModal()">&times;</span>
        <h2>Select Payment Option</h2>
        <div class="payment-buttons">
        <button id="cash-btn" onclick="handleCashPayment()">Cash</button>
                    <button id="gcash-btn" onclick="selectPayment('gcash')">GCash</button>
        </div>
    </div>
</div>
<!-- Modified GCash Modal -->
<div id="gcashModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeGcashModal()">&times;</span>
        <h2><i class="fas fa-qrcode"></i> GCash Payment</h2>
        <div style="margin: 20px 0;">
            <p>Send payment to:</p>
            <img id="gcash-qr" src="gcash-qr.png" alt="GCash QR Code" 
                 style="border: 2px solid #eee; padding: 10px; border-radius: 10px;">

        </div>
        
        <div style="text-align: left; margin: 20px 0;">
            <label style="display: block; margin-bottom: 8px;">
                <i class="fas fa-receipt"></i> GCash Reference Number:
            </label>
            <input type="text" id="gcash-reference" 
                   placeholder="Enter 13-digit GCash reference number"
                   style="width: 100%; padding: 10px; margin-bottom: 15px;"
                   pattern="[0-9]{13}" required>
            
            <label style="display: block; margin-bottom: 8px;">
                <i class="fas fa-file-upload"></i> Upload Payment Proof:
            </label>
            <input type="file" id="gcash-proof" 
                   accept="image/*, .pdf" 
                   style="margin-bottom: 20px;">
        </div>
        
        <button onclick="submitGcashPayment()" 
        style="background-color: #28a745; color: white; padding: 12px 25px;">
    <i class="fas fa-check-circle"></i> Confirm Payment
</button>
    </div>
</div>
<div style="text-align: center; margin-top: 30px;">
    <button type="button" class="submit-btn" onclick="openPaymentModal()">
        <i class="fas fa-paper-plane"></i> Submit Request
    </button>
</div>
    <!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeSuccessModal()">&times;</span>
        <h2>Request Submitted</h2>
        <p>Your request was submitted successfully!</p>
        <p>Reference Number: <span class="ref-number" id="ref-number"></span></p>
        <p>Please take a Screenshot or Save the Reference number</p>
        <p>Please wait for the staff to approve it and await your schedule for pickup.</p>
    </div>
</div>

<script>
function handleCashPayment() {
    const formData = new FormData();
    formData.append('payment_method', 'cash');

    fetch('submit_cedula.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            openSuccessModal(data.reference_number);
            closePaymentModal();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
    });
}

function openPaymentModal() {
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

function selectPayment(method) {
    closePaymentModal();
    if (method === 'cash') {
        handleCashPayment();
    } else if (method === 'gcash') {
        document.getElementById('gcashModal').style.display = 'flex';
    }
}

function closeGcashModal() {
    document.getElementById('gcashModal').style.display = 'none';
}

function submitGcashPayment() {
    const refNumber = document.getElementById('gcash-reference').value;
    const proofFile = document.getElementById('gcash-proof').files[0];

    if (!refNumber || !proofFile) {
        alert('Please complete all GCash payment details');
        return;
    }

    const formData = new FormData();
    formData.append('payment_method', 'gcash');
    formData.append('gcash_reference', refNumber);
    formData.append('payment_proof', proofFile);

    fetch('submit_cedula.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            openSuccessModal(data.reference_number);
            closeGcashModal();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your payment');
    });
}

function openSuccessModal(refNumber) {
    document.getElementById("ref-number").textContent = refNumber;
    document.getElementById("successModal").style.display = 'flex';
}

function closeSuccessModal() {
    document.getElementById("successModal").style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('paymentModal')) {
        closePaymentModal();
    }
    if (event.target == document.getElementById('gcashModal')) {
        closeGcashModal();
    }
    if (event.target == document.getElementById('successModal')) {
        closeSuccessModal();
    }
};

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const hamburger = document.querySelector('.hamburger');
    sidebar.classList.toggle('active');
    hamburger.classList.toggle('open');
}

function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const hamburger = document.querySelector('.hamburger');
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
        hamburger.classList.remove('open');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    <?php if (isset($_SESSION['success'])): ?>
        const refNumber = "<?php echo addslashes($_SESSION['success']); ?>";
        openSuccessModal(refNumber);
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        alert("<?php echo addslashes($_SESSION['error']); ?>");
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});
</script>
</body>
</html>
