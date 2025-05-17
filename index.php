<?php
// 获取客户端和服务器信息
$rawDomain = $_SERVER['HTTP_HOST'] ?? '未知';
$domain = idn_to_utf8($rawDomain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $rawDomain;
$ip = $_SERVER['REMOTE_ADDR'] ?? '未知';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '未知';
$phpVersion = phpversion();
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '未知';
$currentTime = date('Y-m-d H:i:s');
$serverTime = date('Y-m-d H:i:s');

// 检查时间是否一致（摆设，检测不了）
$timeDiff = abs(strtotime($currentTime) - strtotime($serverTime));
$timeConsistent = $timeDiff <= 60;

// 检查域名是否包含中文
$isChineseDomain = preg_match('/[\x{4e00}-\x{9fa5}]/u', $domain);

// 将中文域名转换为Punycode用于链接
function convertDomainForLink($url) {
    $parts = parse_url($url);
    if (isset($parts['host'])) {
        $host = idn_to_ascii($parts['host'], IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $newUrl = str_replace($parts['host'], $host, $url);
        return $newUrl ?: $url;
    }
    return $url;
}

// 检查是否设置了暗黑模式cookie
$darkMode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true';

// 多语言支持
$supportedLanguages = ['en' => 'English', 'zh' => '中文'];
$defaultLanguage = 'zh';

// 获取用户首选语言
$userLanguage = $defaultLanguage;
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $supportedLanguages)) {
    $userLanguage = $_GET['lang'];
    setcookie('lang', $userLanguage, time() + (30 * 24 * 60 * 60), '/');
} elseif (isset($_COOKIE['lang']) && array_key_exists($_COOKIE['lang'], $supportedLanguages)) {
    $userLanguage = $_COOKIE['lang'];
}

// 语言字符串定义
$translations = [
    'zh' => [
        'title' => '当前域名未绑定站点',
        'subtitle' => '小朋友，你似乎走错地方了捏~',
        'domain_info' => '当前访问域名',
        'domain' => '域名',
        'punycode' => 'Punycode',
        'status' => '状态',
        'unbound' => '未绑定站点',
        'time' => '时间',
        'faq' => '常见问题',
        'q1' => 'Q：为什么我会看到此卡片？',
        'a1' => 'A：该域名尚未绑定站点，如需帮助，请联系我们',
        'contact' => '联系我们',
        'email' => '邮箱',
        'qq' => 'QQ',
        'group' => '站长交流群',
        'sites' => '旗下站点',
        'client_info' => '客户端信息',
        'ip' => 'IP地址',
        'browser' => '浏览器',
        'visit_time' => '访问时间',
        'server_info' => '服务器信息',
        'php_version' => 'PHP版本',
        'running' => '运行',
        'server_software' => '服务器软件',
        'server_time' => '服务器时间',
        'consistent' => '一致',
        'inconsistent' => '不一致',
        'copyright' => '© 2050 %s 摘星团团',
        'copy_success' => 'QQ号已复制到剪贴板',
        'language_switcher' => '语言',
        'tuan_icp' => 'TuanICP二次元虚拟备案中心',
        'blog' => '摘星团团博客'
    ],
    'en' => [
        'title' => 'Domain Not Bound',
        'subtitle' => 'Oops, you seem to be in the wrong place~',
        'domain_info' => 'Current Domain',
        'domain' => 'Domain',
        'punycode' => 'Punycode',
        'status' => 'Status',
        'unbound' => 'Not bound to any site',
        'time' => 'Time',
        'faq' => 'FAQ',
        'q1' => 'Q: Why am I seeing this page?',
        'a1' => 'A: This domain is not bound to any site. Please contact us if you need help.',
        'contact' => 'Contact Us',
        'email' => 'Email',
        'qq' => 'QQ',
        'group' => 'Webmaster Group',
        'sites' => 'Our Sites',
        'client_info' => 'Client Info',
        'ip' => 'IP Address',
        'browser' => 'Browser',
        'visit_time' => 'Visit Time',
        'server_info' => 'Server Info',
        'php_version' => 'PHP Version',
        'running' => 'Running',
        'server_software' => 'Server Software',
        'server_time' => 'Server Time',
        'consistent' => 'Consistent',
        'inconsistent' => 'Inconsistent',
        'copyright' => '© 2050 %s 摘星团团',
        'copy_success' => 'QQ number copied to clipboard',
        'language_switcher' => 'Language',
        'tuan_icp' => 'TuanICP Anime Virtual ICP Center',
        'blog' => 'Star Catcher Blog'
    ]
];

// 翻译函数
function t($key, $lang = 'zh') {
    global $translations;
    return $translations[$lang][$key] ?? $key;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $userLanguage; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($domain); ?> - <?php echo t('title', $userLanguage); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            --card-bg: white;
            --text-color: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        /* 暗黑模式变量 */
        .dark-mode {
            --primary-color: #818cf8;
            --secondary-color: #a78bfa;
            --dark-color: #e2e8f0;
            --light-color: #1e293b;
            --success-color: #34d399;
            --warning-color: #fbbf24;
            --danger-color: #f87171;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.25), 0 4px 6px -2px rgba(0, 0, 0, 0.15);
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --card-bg: #1e293b;
            --text-color: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Microsoft YaHei', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            line-height: 1.6;
            transition: var(--transition);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
            word-break: break-all;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            animation: fadeInUp 0.6s ease;
            border: 1px solid var(--border-color);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header i {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            color: var(--primary-color);
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .card-body {
            color: var(--text-secondary);
        }

        .card-body p {
            margin-bottom: 0.75rem;
            word-break: break-word;
        }

        .card-body strong {
            color: var(--text-color);
        }

        .card-body a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            font-weight: normal;
            text-transform: none;
        }

        .card-body a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .scrollable {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .scrollable::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable::-webkit-scrollbar-track {
            background: var(--border-color);
            border-radius: 10px;
        }

        .scrollable::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .scrollable::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        .contact-list {
            list-style: none;
        }

        .contact-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .contact-list i {
            margin-right: 0.5rem;
            width: 1.25rem;
            color: var(--primary-color);
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
            width: 100%;
            color: var(--text-secondary);
            animation: fadeIn 1s ease;
        }

        .footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }


        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ff80e5 0%, #66ccff 80%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
        }

        .toast.show {
            opacity: 1;
        }

        .toast i {
            margin-right: 8px;
        }


        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
            }
        }


        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            margin-left: 0.5rem;
            vertical-align: middle;
        }
        
        /* 中文域名显示优化 */
        .chinese-domain {
            font-family: 'Microsoft YaHei', sans-serif;
        }
        
        /* 旗下站点链接样式 */
        .site-link {
            display: block;
            margin-bottom: 0.75rem;
        }
        .site-link i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .time-tag {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
            vertical-align: middle;
        }
        
        .consistent {
            background-color: var(--success-color);
            color: white;
        }
        
        .inconsistent {
            background-color: var(--danger-color);
            color: white;
        }
        
        /* 运行标签样式 */
        .running-tag {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background: linear-gradient(to right, var(--success-color), #3b82f6);
            color: white;
            margin-right: 0.5rem;
            vertical-align: middle;
        }
        
        /* 暗黑模式切换按钮 */
        .dark-mode-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: var(--transition);
        }
        
        .dark-mode-toggle:hover {
            transform: scale(1.1);
        }
        
        .dark-mode-toggle i {
            font-size: 1.25rem;
            color: var(--primary-color);
        }
        
        /* 语言切换按钮 */
        .language-switcher {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: var(--transition);
        }
        
        .language-switcher:hover {
            transform: scale(1.1);
        }
        
        .language-switcher i {
            font-size: 1.25rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body class="<?php echo $darkMode ? 'dark-mode' : ''; ?>">
    <div class="container">
        <header class="header">
            <h1 class="chinese-domain"><?php echo t('title', $userLanguage); ?></h1>
            <p><?php echo t('subtitle', $userLanguage); ?></p>
        </header>

        <div class="cards-container">
            <!-- 域名信息卡片 -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-globe"></i>
                    <h2><?php echo t('domain_info', $userLanguage); ?></h2>
                </div>
                <div class="card-body">
                    <p><strong><?php echo t('domain', $userLanguage); ?>：</strong> <span class="chinese-domain"><?php echo htmlspecialchars($domain); ?></span></p>
                    <?php if($isChineseDomain): ?>
                        <p><strong><?php echo t('punycode', $userLanguage); ?>：</strong> <?php echo htmlspecialchars($rawDomain); ?></p>
                    <?php endif; ?>
                    <p><strong><?php echo t('status', $userLanguage); ?>：</strong> <span class="badge"><?php echo t('unbound', $userLanguage); ?></span></p>
                    <p><strong><?php echo t('time', $userLanguage); ?>：</strong> <?php echo $currentTime; ?></p>
                </div>
            </div>

            <!-- 帮助信息卡片 -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-question-circle"></i>
                    <h2><?php echo t('faq', $userLanguage); ?></h2>
                </div>
                <div class="card-body">
                    <p><strong><?php echo t('q1', $userLanguage); ?></strong></p>
                    <p><?php echo t('a1', $userLanguage); ?></p>
                </div>
            </div>

            <!-- 联系信息卡片 -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-envelope"></i>
                    <h2><?php echo t('contact', $userLanguage); ?></h2>
                </div>
                <div class="card-body">
                    <ul class="contact-list">
                        <li><i class="fas fa-envelope"></i> <?php echo t('email', $userLanguage); ?>：<a href="mailto:ccssna@qq.com">ccssna@qq.com</a></li>
                        <li><i class="fab fa-qq"></i> <?php echo t('qq', $userLanguage); ?>：<a class="copy-qq" data-qq="937319686">937319686</a></li>
                        <li><i class="fas fa-users"></i> <?php echo t('group', $userLanguage); ?>：<a href="https://qm.qq.com/q/uEKQOqoAmW" target="_blank">967140086</a></li>
                    </ul>
                </div>
            </div>

            <!-- 旗下站点卡片 -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-link"></i>
                    <h2><?php echo t('sites', $userLanguage); ?></h2>
                </div>
                <div class="card-body">
                    <a href="https://icp.星.fun" target="_blank" class="site-link">
                        <i class="fas fa-external-link-alt"></i><?php echo t('tuan_icp', $userLanguage); ?>
                    </a>
                    <a href="https://博客.星.fun" target="_blank" class="site-link">
                        <i class="fas fa-external-link-alt"></i><?php echo t('blog', $userLanguage); ?>
                    </a>
                </div>
            </div>

            <!-- 客户端信息卡片 -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-desktop"></i>
                    <h2><?php echo t('client_info', $userLanguage); ?></h2>
                </div>
                <div class="card-body scrollable">
                    <p><strong><?php echo t('ip', $userLanguage); ?>：</strong> <?php echo htmlspecialchars($ip); ?></p>
                    <p><strong><?php echo t('browser', $userLanguage); ?>：</strong> <?php echo htmlspecialchars($userAgent); ?></p>
                    <p><strong><?php echo t('visit_time', $userLanguage); ?>：</strong> <?php echo $currentTime; ?></p>
                </div>
            </div>


            <!-- 服务器信息卡片 -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-server"></i>
                    <h2><?php echo t('server_info', $userLanguage); ?></h2>
                </div>
                <div class="card-body scrollable">
                    <p><strong><?php echo t('php_version', $userLanguage); ?>： </strong> <span class="running-tag"><?php echo t('running', $userLanguage); ?> <?php echo htmlspecialchars($phpVersion); ?></span></p>
                    <p><strong><?php echo t('server_software', $userLanguage); ?>：</strong> <span class="running-tag"><?php echo htmlspecialchars($serverSoftware); ?> <?php echo t('running', $userLanguage); ?></span></p>
                    <p><strong><?php echo t('server_time', $userLanguage); ?>：</strong> <?php echo $serverTime; ?> 
                        <span class="time-tag <?php echo $timeConsistent ? 'consistent' : 'inconsistent'; ?>">
                            <?php echo $timeConsistent ? t('consistent', $userLanguage) : t('inconsistent', $userLanguage); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <footer class="footer">
            <p><?php echo sprintf(t('copyright', $userLanguage), date('Y')); ?> | <a href="https://icp.xn--kiv.fun/id.php?keyword=20253777" target="_blank">团ICP备20253777号</a></p>
        </footer>
    </div>

    <!-- 暗黑模式切换按钮 -->
    <div class="dark-mode-toggle" id="darkModeToggle">
        <i class="<?php echo $darkMode ? 'fas fa-sun' : 'fas fa-moon'; ?>"></i>
    </div>
    
    <!-- 语言切换按钮 -->
    <div class="language-switcher" id="languageSwitcher">
        <i class="fas fa-language"></i>
    </div>

    <!-- 复制成功提示弹窗 -->
    <div id="toast" class="toast">
        <i class="fas fa-check-circle"></i>
        <span><?php echo t('copy_success', $userLanguage); ?></span>
    </div>

    <script>
        // 添加卡片悬停动画
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // QQ号复制功能
            const qqLinks = document.querySelectorAll('.copy-qq');
            const toast = document.getElementById('toast');
            
            qqLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const qqNumber = this.getAttribute('data-qq');
                    
                    // 复制到剪贴板
                    navigator.clipboard.writeText(qqNumber).then(() => {
                        // 显示提示
                        toast.classList.add('show');
                        
                        // 3秒后隐藏提示
                        setTimeout(() => {
                            toast.classList.remove('show');
                        }, 3000);
                    }).catch(err => {
                        console.error('复制失败:', err);
                        // 如果剪贴板API不可用，使用老方法
                        const textarea = document.createElement('textarea');
                        textarea.value = qqNumber;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        
                        // 显示提示
                        toast.classList.add('show');
                        
                        // 3秒后隐藏提示
                        setTimeout(() => {
                            toast.classList.remove('show');
                        }, 3000);
                    });
                });
            });
            
            // 暗黑模式切换功能
            const darkModeToggle = document.getElementById('darkModeToggle');
            const body = document.body;
            
            darkModeToggle.addEventListener('click', function() {
                const isDarkMode = body.classList.contains('dark-mode');
                
                // 切换类名
                body.classList.toggle('dark-mode');
                
                // 更新图标
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-sun');
                icon.classList.toggle('fa-moon');
                
                // 设置cookie，30天过期
                const expires = new Date();
                expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000));
                document.cookie = `darkMode=${!isDarkMode}; expires=${expires.toUTCString()}; path=/`;
            });
            
            // 语言切换功能
            const languageSwitcher = document.getElementById('languageSwitcher');
            
            languageSwitcher.addEventListener('click', function() {
                const currentLang = '<?php echo $userLanguage; ?>';
                const newLang = currentLang === 'zh' ? 'en' : 'zh';
                
                // 重定向页面并设置语言参数
                const url = new URL(window.location.href);
                url.searchParams.set('lang', newLang);
                window.location.href = url.toString();
            });
        });
    </script>
</body>
</html>
