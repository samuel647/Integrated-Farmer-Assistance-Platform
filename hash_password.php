 <?php
/**
 * Agri-Tech CM Password Utility
 * Use this to generate hashes for manual database entry.
 */

$password_to_hash = "Adm0510NKONGHO@!"; // <--- Change this to the password you want
$hashed_value = password_hash($password_to_hash, PASSWORD_BCRYPT);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hash Utility | Agri-Tech</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-2xl w-full bg-slate-800 rounded-[2rem] p-10 shadow-2xl border border-white/5">
        <h1 class="text-green-500 font-black uppercase tracking-widest text-xs mb-4">Password Hash Utility</h1>
        
        <div class="mb-8">
            <label class="block text-slate-500 text-[10px] uppercase font-bold mb-2">Original Password</label>
            <p class="text-white font-mono bg-slate-900/50 p-4 rounded-xl border border-white/5"><?php echo $password_to_hash; ?></p>
        </div>

        <div class="mb-8">
            <label class="block text-slate-500 text-[10px] uppercase font-bold mb-2">Secure Hash (BCRYPT)</label>
            <textarea readonly class="w-full h-32 text-yellow-500 font-mono bg-slate-900/50 p-4 rounded-xl border border-white/5 outline-none resize-none"><?php echo $hashed_value; ?></textarea>
            <p class="text-slate-400 text-[10px] mt-3 italic">Copy this long string and paste it into the 'password' column in phpMyAdmin.</p>
        </div>

        <div class="bg-blue-500/10 border border-blue-500/20 p-6 rounded-2xl">
            <p class="text-blue-400 text-xs leading-relaxed">
                <strong>Note:</strong> Every time you refresh this page, the hash will change even if the password is the same. This is normal! BCRYPT uses a random "salt" for every hash to ensure maximum security.
            </p>
        </div>
    </div>

</body>
</html>