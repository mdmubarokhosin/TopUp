<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Cards</title>
    <style>
        .support-cards {
            display: flex;
            justify-content: center;
            align-items: stretch;
            gap: 8px;
            max-width: 1100px;
            margin-top: 14px;
            margin-bottom: 2px;
            padding: 0 8px;
            flex-wrap: nowrap;
            box-sizing: border-box;
        }

        .support-card {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            flex: 1 1 0;
            background: #2563eb; 
            border-radius: 8px;
            padding: 10px 12px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);
            text-decoration: none;
            color: #ffffff;
            transition: transform 0.15s ease, background-color 0.15s ease;
            min-width: 0;
        }

        .support-card:hover {
            transform: translateY(-1px);
            background: #1d4ed8;
        }

        .support-card .icon {
            width: 30px;
            height: 30px;
            min-width: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #ffffff;
        }

        .support-card .icon svg {
            width: 16px;
            height: 16px;
            object-fit: contain;
        }

        .support-card .meta {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
            text-align: left;
            line-height: 1.2;
        }

        .support-card .label {
            font-size: 8.5px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        .support-card .title {
            font-size: 11.5px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }

        @media (max-width: 600px) {
            .support-cards {
                gap: 6px;
                padding: 0 6px;
                margin-top: 10px;
                margin-bottom: 0px;
            }
            
            .support-card {
                padding: 8px 8px;
                gap: 6px;
                border-radius: 7px;
            }
            
            .support-card .icon {
                width: 25px;
                height: 25px;
                min-width: 25px;
            }
            
            .support-card .icon svg {
                width: 13px;
                height: 13px;
            }
            
            .support-card .label {
                font-size: 7.5px;
            }
            
            .support-card .title {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <section class="container m-auto">
        <section class="my-0" id="topup"> 
            <div class="container mx-auto">
                <div class="text-center">
                    <div class="support-cards">
                        
                        <a class="support-card" href="<?php echo getSetting($conn, 'fab_link') ? getSetting($conn, 'fab_link') : '#'; ?>" target="_blank">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2563eb">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15.75-1.05 4.54-1.5 6.94-.19.98-.56 1.31-.91 1.34-.77.07-1.35-.51-2.1-1-1.17-.77-1.83-1.25-2.96-2-1.31-.86-.46-1.34.29-2.12.19-.2 3.6-3.3 3.67-3.6.01-.03.01-.15-.06-.21-.07-.06-.17-.04-.25-.02-.11.02-1.83 1.16-5.16 3.42-.49.34-.93.5-1.33.49-.44-.01-1.29-.25-1.92-.45-.77-.25-1.39-.39-1.34-.83.03-.23.35-.46.97-.71 3.79-1.65 6.32-2.74 7.59-3.27 3.61-1.5 4.36-1.76 4.85-1.77.11 0 .35.03.5.15.13.1.17.24.19.34.02.13.03.43.01.65z"/>
                                </svg>
                            </div>
                            <div class="meta">
                                <div class="label">Support</div>
                                <div class="title">Telegram</div>
                            </div>
                        </a>
                        
                        <a class="support-card" href="<?php echo getSetting($conn, 'fb_url') ? getSetting($conn, 'fb_url') : '#'; ?>" target="_blank">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2563eb">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            </div>
                            <div class="meta">
                                <div class="label">Group</div>
                                <div class="title">Join Group</div>
                            </div>
                        </a>
                        
                        <a class="support-card" href="<?php echo getSetting($conn, 'messenger_url') ? getSetting($conn, 'messenger_url') : '#'; ?>" target="_blank">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2563eb">
                                    <path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.93 1.19 5.54 3.12 7.42.16.16.26.38.24.61l-.15 2.13c-.04.57.53.98 1.03.73l2.39-1.17c.18-.09.4-.1.58-.04 1.13.34 2.34.52 3.59.52 5.64 0 10-4.13 10-9.7S17.64 2 12 2zm1.32 12.16l-2.43-2.6c-.4-.43-1.09-.43-1.49 0l-3.37 3.6c-.45.48-1.12-.13-.73-.65l3.65-4.87c.4-.53 1.21-.53 1.61 0l2.43 2.6c.4.43 1.09.43 1.49 0l3.41-3.65c.45-.48 1.12.13.73.65l-3.65 4.87c-.41.55-1.21.55-1.61.05z"/>
                                </svg>
                            </div>
                            <div class="meta">
                                <div class="label">Support</div>
                                <div class="title">Messenger</div>
                            </div>
                        </a>

                    </div>
                </div>
            </div>
        </section>
    </section>
</body>
</html>