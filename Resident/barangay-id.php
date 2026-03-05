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
    <title>Barangay ID</title>
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
        .payment-buttons button#cash-btn {
            background-color: #28a745;
            color: white;
        }

        .payment-buttons button#cash-btn:hover {
            background-color: #218838;
        }

        /* payment-buttons layout is kept but only cash option will be presented */
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

        .modal .cancel-btn:hover {
            background-color: #c82333;
        }
        .sidebar a.active {
    background-color: #007bff; 
    color: white;
    font-weight: bold; 
    border-radius: 5px;
    padding: 10px;
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
        <h1>Request Barangay ID</h1>
        <div class="form-container">
        <form id="barangay-id-form" action="submit_id.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="payment_method" id="payment_method" value="cash">
        <h2 style="color: #003566; font-size: 20px; margin-bottom: 10px;">In Case of Emergency Contact</h2>
                <label for="emergency-name">Name:</label>
                <input type="text" id="emergency-name" name="emergency_name" placeholder="Enter emergency contact name" required>
                <label for="emergency-address">Address:</label>
                <input type="text" id="emergency-address" name="emergency_address" placeholder="Enter emergency contact address" required>
                <label for="emergency-contact">Contact Number:</label>
                <input type="text" id="emergency-contact" name="emergency_contact" placeholder="Enter emergency contact number" maxlength="11" required pattern="\d{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <label for="relationship">Relationship:</label>
                <input type="text" id="relationship" name="relationship" placeholder="Enter relationship with emergency contact" required>
                <label for="id-type">Select Valid ID Type:</label>
<select id="id-type" name="id_type" required>
    <option value="">Choose ID Type</option>
    <option value="Driver’s License">Driver’s License</option>
    <option value="Passport">Passport</option>
    <option value="Barangay ID">Barangay ID</option>
    <option value="SSS Card">SSS Card</option>
    <option value="GSIS Card">GSIS Card</option>
    <option value="NBI Clearance">NBI Clearance</option>
    <option value="PhilHealth ID">PhilHealth ID</option>
    <option value="PRC Card">PRC Card</option>
    <option value="Postal ID">Postal ID</option>
    <option value="TIN ID">TIN ID</option>
    <option value="National ID">National ID</option>
</select>
                <label for="valid-id">Attach Your Valid ID:</label>
                <input type="file" id="valid-id" name="valid_id" accept="image/*" required>
                <p class="note">Attach a clear image of your VALID ID. Preferably DRIVER'S LICENSE or PASSPORT.</p>
                <br>
                <div style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 5px;">
        <p style="color: #155724; margin: 0;">
            <i class="fas fa-money-bill-wave"></i> 
            <strong>Payment Required:</strong> A fee of ₱150.00 must be paid upon submission
        </p>
    </div>
    <br>
    <br>
                <button type="button" class="submit-btn" onclick="openConfirmationModal()">Submit</button>
            </form>
        </div>
    </div>

    <div id="confirmationModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeConfirmationModal()">&times;</span>
        <h3>Confirm Your Details</h3>
        <p><strong>Emergency Name:</strong> <span id="confirm-emergency-name"></span></p>
        <p><strong>Emergency Address:</strong> <span id="confirm-emergency-address"></span></p>
        <p><strong>Emergency Contact:</strong> <span id="confirm-emergency-contact"></span></p>
        <p><strong>Relationship:</strong> <span id="confirm-relationship"></span></p>
        <button class="confirm-btn" onclick="confirmAndSubmit()">Confirm & Submit</button>
        <button class="cancel-btn" onclick="closeConfirmationModal()">Cancel</button>
    </div>
</div>

    
    <div id="instructionModal" class="modal">
        <div class="modal-content">
        <span class="close-button" onclick="closeInstructionModal()">&times;</span>
            <h2>Application Submitted</h2>
            <p>Your application was submitted successfully!</p>
            <p>Reference Number: <span class="ref-number" id="ref-number"></span></p>
            <p>Please take a Screenshot or Save the Reference number</p>
            <p>Please wait for the staff to approve it and await your schedule for pickup.</p>
        </div>
    </div>

<!-- cash is the only payment option, no modal needed -->
        <!-- closing div from earlier form container -->
</div>

    <script>
        // cash only payment helper
        function handleCashPayment() {
            const formData = new FormData(document.getElementById('barangay-id-form'));
            formData.append('payment_method', 'cash');

            fetch('submit_id.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON from server:', text);
                    alert('Server error: ' + text);
                    return;
                }

                if (data.success) {
                    openInstructionModal(data.reference_number);
                } else {
                    alert(data.message || 'An error occurred while processing your request.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request. Please try again.');
            });
        }


document.getElementById('barangay-id-form').addEventListener('submit', function(e) {
    const requiredFields = [
        'emergency-name', 'emergency-address', 'emergency-contact', 'relationship', 'valid-id'
    ];
    
    let isValid = true;
    requiredFields.forEach(id => {
        const field = document.getElementById(id);
        if (!field.value.trim()) {
            field.style.border = '1px solid #dc3545';
            isValid = false;
        } else {
            field.style.border = '';
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
function openSuccessModal(refNumber) {
    document.getElementById("ref-number").textContent = refNumber;
    document.getElementById("successModal").style.display = 'flex';
}

function closeSuccessModal() {
    document.getElementById("successModal").style.display = 'none';
}

window.onclick = function(event) {
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
        </script>
        <script>
        let confirmationData = {};

        function openConfirmationModal() {
    const requiredFields = [
        'emergency-name', 'emergency-address', 'emergency-contact', 'relationship', 'valid-id'
    ];
    
    let isValid = true;
    requiredFields.forEach(id => {
        const field = document.getElementById(id);
        if (!field.value.trim()) {
            field.style.border = '1px solid #dc3545';
            isValid = false;
        } else {
            field.style.border = '';
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields.');
        return;
    }

    confirmationData = {
        emergencyName: document.getElementById('emergency-name').value,
        emergencyAddress: document.getElementById('emergency-address').value,
        emergencyContact: document.getElementById('emergency-contact').value,
        relationship: document.getElementById('relationship').value,
    };

    document.getElementById('confirm-emergency-name').innerText = confirmationData.emergencyName;
    document.getElementById('confirm-emergency-address').innerText = confirmationData.emergencyAddress;
    document.getElementById('confirm-emergency-contact').innerText = confirmationData.emergencyContact;
    document.getElementById('confirm-relationship').innerText = confirmationData.relationship;

    document.getElementById('confirmationModal').style.display = 'flex';
}

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

       function confirmAndSubmit() {
        closeConfirmationModal();
        // submit immediately via cash
        handleCashPayment();
    }
        

        function openInstructionModal(refNumber) {
            document.getElementById("ref-number").innerText = refNumber; 
            document.getElementById("instructionModal").style.display = 'flex';
        }

        function closeInstructionModal() {
            document.getElementById("instructionModal").style.display = 'none';
        }

        window.onclick = function (event) {
            const confirmationModal = document.getElementById("confirmationModal");
            const instructionModal = document.getElementById("instructionModal");
            if (event.target == confirmationModal) {
                confirmationModal.style.display = 'none';
            }
            if (event.target == instructionModal) {
                instructionModal.style.display = 'none';
            }
        };

        document.addEventListener("DOMContentLoaded", function () {
        
        });
    </script>
</body>
</html>
