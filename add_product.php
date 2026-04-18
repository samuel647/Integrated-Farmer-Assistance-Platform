<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = htmlspecialchars($_POST['description']);
    $farmer_id = $_SESSION['user_id'];

    // --- Image Upload Logic ---
    $image_path = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $upload_dir = 'uploads/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                $error = "Failed to move uploaded file to the server folder.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, and WEBP are allowed.";
        }
    } else {
        $error = "Please select a product image to upload.";
    }

    // --- Database Entry ---
    if (empty($error)) {
        try {
            $sql = "INSERT INTO products (name, price, category, description, image_url, farmer_id, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $price, $category, $description, $image_path, $farmer_id])) {
                $message = "Yield listing submitted successfully! Redirecting to dashboard in 3 seconds...";
                // Redirect back to dashboard after a short delay
                header("refresh:3;url=landing.php"); 
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Yield | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fcfdfa] text-slate-900 p-4 md:p-12">

    <div class="max-w-2xl mx-auto mb-8 flex items-center justify-between">
        <a href="landing.php" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-green-800 transition-all">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <div class="text-right">
            <span class="block font-black text-sm tracking-tighter uppercase leading-none text-slate-900">Agri-Tech <span class="text-[#8b5e34]">CM</span></span>
        </div>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-8 md:p-14 rounded-[3.5rem] shadow-2xl shadow-green-900/5 border border-slate-100">
        
        <header class="mb-10">
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2">Register <span class="text-green-800 italic">Harvest</span></h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Institutional Grade Listing Portal</p>
        </header>

        <?php if($message): ?>
            <div class="bg-green-50 border border-green-100 text-green-800 p-6 rounded-3xl mb-8 flex items-center gap-4">
                <i class="fas fa-check-circle text-xl animate-bounce"></i>
                <p class="text-xs font-bold uppercase tracking-widest"><?= $message ?></p>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="bg-red-50 border border-red-100 text-red-800 p-6 rounded-3xl mb-8 flex items-center gap-4">
                <i class="fas fa-exclamation-triangle text-xl"></i>
                <p class="text-xs font-bold uppercase tracking-widest"><?= $error ?></p>
            </div>
        <?php endif; ?>

        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            
            <div class="group">
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-2">Product Visual (Required)</label>
                <div class="relative border-2 border-dashed border-slate-200 rounded-[2rem] p-8 text-center hover:border-green-800 transition-all bg-slate-50/50">
                    <input type="file" name="product_image" id="imgInput" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div id="preview-placeholder">
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 mb-3 group-hover:text-green-800 transition-colors"></i>
                        <p class="text-xs font-bold text-slate-500">Tap to upload harvest photo</p>
                        <p class="text-[9px] text-slate-400 mt-1 uppercase tracking-tighter">PNG, JPG or WEBP (Max 5MB)</p>
                    </div>
                    <img id="imgPreview" class="hidden mx-auto h-32 w-32 object-cover rounded-2xl shadow-lg">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2 tracking-widest">Produce Name</label>
                <input type="text" name="name" required placeholder="e.g. Grade A Cocoa Beans" 
                class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-5 outline-none focus:bg-white focus:border-green-800 focus:ring-4 focus:ring-green-800/5 font-bold text-sm transition-all">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2 tracking-widest">Target Price (XAF/kg)</label>
                    <input type="number" name="price" required placeholder="0.00" 
                    class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-5 outline-none focus:bg-white focus:border-green-800 font-bold text-sm transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2 tracking-widest">Category</label>
                    <div class="relative">
                        <select name="category" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-5 outline-none focus:bg-white focus:border-green-800 font-bold text-sm transition-all appearance-none cursor-pointer">
                            <option value="Tubers">Tubers & Roots</option>
                            <option value="Grains">Grains & Cereals</option>
                            <option value="Fruits">Fresh Fruits</option>
                            <option value="Vegetables">Vegetables</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2 tracking-widest">Quality Description</label>
                <textarea name="description" rows="4" placeholder="Mention harvest date, moisture content, etc..." 
                class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-5 outline-none focus:bg-white focus:border-green-800 font-bold text-sm transition-all"></textarea>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] hover:bg-green-800 transition-all shadow-2xl shadow-slate-900/30 active:scale-[0.98]">
                Submit Harvest for Review
            </button>
        </form>
    </div>

    <script>
        const imgInput = document.getElementById('imgInput');
        const imgPreview = document.getElementById('imgPreview');
        const placeholder = document.getElementById('preview-placeholder');

        imgInput.onchange = evt => {
            const [file] = imgInput.files;
            if (file) {
                imgPreview.src = URL.createObjectURL(file);
                imgPreview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
        }
    </script>
</body>
</html>