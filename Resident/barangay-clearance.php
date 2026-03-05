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
    <title>Barangay Clearance</title>
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
        .input-group {
    margin: 15px 0;
}

#reference-number {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

.input-group label {
    color: #003566;
    font-size: 14px;
    font-weight: bold;
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
        <h1>Request of Barangay Clearance</h1>
        <div class="form-container">
        <form action="submit_clearance.php" method="POST" enctype="multipart/form-data">
    <label for="purpose">Purpose:</label>
    <textarea id="purpose" name="purpose" rows="3" placeholder="Enter purpose for the clearance" required></textarea>
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
    <label for="valid-id">Attach Valid ID:</label>
    <input type="file" id="valid-id" name="valid_id" accept="image/*" required>
    <p class="note">Attach a clear image of your VALID ID. Preferably BRGY ID, DRIVER'S LICENSE or PASSPORT.</p>
    <br>

    <button type="button" class="submit-btn" onclick="openConfirmModal()">Submit</button>
    </form>
        </div>
    </div>
    <div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeConfirmModal()">&times;</span>
        <h3>Confirm Your Details</h3>
        <p><strong>Purpose:</strong> <span id="confirm-purpose"></span></p>
        <p><strong>Valid ID:</strong> <span id="confirm-valid-id"></span></p>
        <button class="confirm-btn" onclick="submitForm()">Confirm & Submit</button>
        <button class="cancel-btn" onclick="closeConfirmModal()">Cancel</button>
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
    <div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeConfirmModal()">&times;</span>
        <h3>Confirm Your Details</h3>
        <p><strong>Purpose:</strong> <span id="confirm-purpose"></span></p>
        <p><strong>ID Type:</strong> <span id="confirm-id-type"></span></p> <!-- Add this line -->
        <p><strong>Valid ID:</strong> <span id="confirm-valid-id"></span></p>
        <button class="confirm-btn" onclick="submitForm()">Confirm & Submit</button>
        <button class="cancel-btn" onclick="closeConfirmModal()">Cancel</button>
    </div>
</div>
    <script>
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

    function openConfirmModal() {
        const purpose = document.getElementById('purpose').value;
        const idType = document.getElementById('id-type').value;
        const validId = document.getElementById('valid-id').files[0]?.name || "No file selected";

        document.getElementById('confirm-purpose').textContent = purpose;
        document.getElementById('confirm-id-type').textContent = idType; 
        document.getElementById('confirm-valid-id').textContent = validId;

        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function submitForm() {
        document.querySelector('form').submit(); 
    }

    function openInstructionModal(refNumber) {
        document.getElementById("ref-number").textContent = refNumber;
        document.getElementById("instructionModal").style.display = 'flex';
    }

    function closeInstructionModal() {
        document.getElementById("instructionModal").style.display = 'none';
    }

    window.onclick = function (event) {
        const confirmModal = document.getElementById("confirmModal");
        const instructionModal = document.getElementById("instructionModal");
        
        if (event.target === confirmModal) {
            confirmModal.style.display = 'none';
        }
        if (event.target === instructionModal) {
            instructionModal.style.display = 'none';
        }
    };

    document.addEventListener("DOMContentLoaded", function () {
        <?php if (isset($_SESSION['success'])): ?>
            const refNumber = "<?php echo addslashes($_SESSION['success']); ?>";
            openInstructionModal(refNumber);
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
