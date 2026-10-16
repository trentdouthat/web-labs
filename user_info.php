<?php
// Request high-entropy client hints from the browser
header("Accept-CH: sec-ch-ua-platform-version, sec-ch-ua-model, sec-ch-ua-full-version-list");
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server-Side Connection Info (PHP)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-900 font-sans antialiased text-slate-200 p-4 md:p-8">

    <div class="max-w-4xl mx-auto">
        <header class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-white tracking-tight">PHP Server Insights</h1>
            <p class="text-slate-400 mt-3 text-lg">Detailed server-side analytics from the Linux environment</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Critical Network Info -->
            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg border border-slate-700">
                <span class="text-xs font-bold uppercase text-cyan-400 tracking-widest">Remote IP</span>
                <p class="text-2xl font-mono mt-2 text-white">
                    <?php echo $_SERVER['REMOTE_ADDR']; ?>
                </p>
            </div>

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg border border-slate-700">
                <span class="text-xs font-bold uppercase text-emerald-400 tracking-widest">Protocol</span>
                <p class="text-2xl font-mono mt-2 text-white">
                    <?php echo $_SERVER['SERVER_PROTOCOL']; ?>
                </p>
            </div>

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg border border-slate-700">
                <span class="text-xs font-bold uppercase text-amber-400 tracking-widest">Request Method</span>
                <p class="text-2xl font-mono mt-2 text-white">
                    <?php echo $_SERVER['REQUEST_METHOD']; ?>
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Detailed Client Hints (New Section) -->
            <section class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700">
                <div class="bg-slate-700/50 px-6 py-3 border-b border-slate-700">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Detailed Client Hints (High Entropy)</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-slate-500 font-mono">Platform Version (Exact OS)</span>
                        <p class="text-sm font-mono text-cyan-400">
                            <?php echo isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION']) ? $_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'] : 'Not provided (Requires HTTPS/Refresh)'; ?>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-mono">Device Model</span>
                        <p class="text-sm font-mono text-cyan-400">
                            <?php echo isset($_SERVER['HTTP_SEC_CH_UA_MODEL']) ? $_SERVER['HTTP_SEC_CH_UA_MODEL'] : 'Not provided'; ?>
                        </p>
                    </div>
                </div>
            </section>

            <!-- Server Paths -->
            <section class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700">
                <div class="bg-slate-700/50 px-6 py-3 border-b border-slate-700">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Filesystem & Server Paths</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-xs text-slate-500 font-mono">DOCUMENT_ROOT</span>
                        <p class="text-sm font-mono text-slate-200 break-all"><?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-mono">SCRIPT_FILENAME</span>
                        <p class="text-sm font-mono text-slate-200 break-all"><?php echo $_SERVER['SCRIPT_FILENAME']; ?></p>
                    </div>
                </div>
            </section>

            <!-- Software Environment -->
            <section class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700">
                <div class="bg-slate-700/50 px-6 py-3 border-b border-slate-700">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Software Environment</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-slate-500 font-mono">SERVER_SOFTWARE</span>
                        <p class="text-sm font-mono text-slate-200"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-mono">PHP_VERSION</span>
                        <p class="text-sm font-mono text-slate-200"><?php echo phpversion(); ?></p>
                    </div>
                </div>
            </section>

            <!-- All Request Headers -->
            <section class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700">
                <div class="bg-slate-700/50 px-6 py-3 border-b border-slate-700">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">HTTP Request Headers</h2>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs font-bold text-slate-500 uppercase border-b border-slate-700">
                                <th class="px-6 py-4">Header</th>
                                <th class="px-6 py-4">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php
                            // Check if function exists (some servers disable it)
                            if (function_exists('apache_request_headers')) {
                                $headers = apache_request_headers();
                                foreach ($headers as $header => $value) {
                                    echo "<tr>";
                                    echo "<td class='px-6 py-3 text-xs font-mono text-cyan-400'>$header</td>";
                                    echo "<td class='px-6 py-3 text-xs font-mono text-slate-300 break-all'>$value</td>";
                                    echo "</tr>";
                                }
                            } else {
                                // Fallback for servers where apache_request_headers() is unavailable
                                foreach ($_SERVER as $key => $value) {
                                    if (substr($key, 0, 5) == 'HTTP_') {
                                        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                                        echo "<tr>";
                                        echo "<td class='px-6 py-3 text-xs font-mono text-cyan-400'>$header</td>";
                                        echo "<td class='px-6 py-3 text-xs font-mono text-slate-300 break-all'>$value</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <footer class="mt-12 text-center pb-8">
            <p class="text-slate-500 text-sm">
                This page uses <strong>PHP</strong> and <strong>Client Hints</strong>. 
                Exact OS versions (like "13.0.1") often require an initial handshake and a secure HTTPS connection.
            </p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="user_info.html" class="text-slate-400 hover:text-white transition-colors text-sm underline decoration-slate-600">
                    View Client-Side Version
                </a>
                <span class="text-slate-700">|</span>
                <a href="rgb_viewer.html" class="text-slate-400 hover:text-white transition-colors text-sm underline decoration-slate-600">
                    RGB Viewer
                </a>
            </div>
        </footer>
    </div>

</body>
</html>