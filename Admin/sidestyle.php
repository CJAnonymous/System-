<style>
    .sidebar a.active {
    background-color: #007bff; /* Highlight color */
    color: white; /* Text color */
    font-weight: bold; /* Optional: Makes it stand out more */
    border-radius: 5px;
    padding: 10px;
}

  * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .sidebar {
            width: 250px;
            background-color: #003566;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
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
            padding: 20px;
            overflow-y: auto;
        }

        .main-content h1 {
            font-size: 28px;
            color: #003566;
            text-align: center;
            margin-bottom: 20px;
        }

        .about-section img {
            max-width: 50%;
            height: auto;
            margin: 20px auto;
            display: block;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .about-section p {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            text-align: justify;
            margin: 20px auto;
            max-width: 800px;
        }

        .sidebar a i {
            font-size: 20px;
        }
         .resident-message {
      font-size: 4.5em;
      font-weight: 900;
      margin-top: 10px;
      text-transform: uppercase;
      opacity: 0;
      
    }
</style>
