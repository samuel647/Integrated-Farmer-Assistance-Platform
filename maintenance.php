<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Upgrades | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfdfa; }
        
        /* Subtle grid background for a "technical" feel */
        .bg-grid {
            background-image: radial-gradient(#064e3b 0.5px, transparent 0.5px);
            background-size: 30px 30px;
            opacity: 0.05;
        }

        /* Animation for the progress bar */
        @keyframes fillProgress {
            from { width: 0%; }
            to { width: 85%; }
        }
        .progress-animate { animation: fillProgress 2.5s ease-out forwards; }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <div class="absolute inset-0 bg-grid"></div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <div class="bg-[#064e3b] text-white w-24 h-24 rounded-[2.5rem] flex items-center justify-center text-3xl mx-auto mb-10 shadow-2xl border-4 border-white">
            <i class="fas fa-tools animate-pulse"></i>
        </div>

        <h1 class="text-6xl font-black tracking-tighter mb-6 leading-none text-slate-900">
            Planting <br><span class="text-[#064e3b] italic">New Features.</span>
        </h1>
        
        <p class="text-slate-500 text-lg font-medium leading-relaxed mb-12 max-w-md mx-auto">
            We are currently optimizing the <b>National Trade Floor</b> to handle increased harvest volumes. Your data and funds remain 100% secure.
        </p>

        <div class="max-w-sm mx-auto mb-12 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Upgrade Status</span>
                <span class="text-[10px] font-black text-[#064e3b]">85% Complete</span>
            </div>
            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="progress-animate h-full bg-[#064e3b] rounded-full"></div>
            </div>
            <p class="mt-4 text-[11px] font-bold text-slate-400">
                <i class="fas fa-clock mr-2"></i> ETA: 15:30 WAT (Today)
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <div class="flex items-center gap-3 bg-green-50 px-5 py-3 rounded-2xl border border-green-100">
                <span class="flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-widest text-green-800">Support Online</span>
            </div>
            
            <a href="mailto:ops@agritech.cm" class="text-xs font-black uppercase tracking-widest text-[#8b5e34] hover:text-[#064e3b] transition-colors">
                Report Urgent Issue <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <footer class="absolute bottom-10 w-full text-center">
        <p class="text-[9px] font-bold tracking-[0.5em] text-slate-300 uppercase">
            © 2026 Agri-Tech CM • Secure National Agriculture Infrastructure
        </p>
    </footer>
</body>
</html>