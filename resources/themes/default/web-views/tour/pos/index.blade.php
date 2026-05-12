<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Booking - Step by Step</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #d1cbcb5e;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 6px;
            padding: 5px;
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 140, 0, 0.3);
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 4px;
            font-weight: 700;
            color: white;
        }

        .subtitle {
            font-size: 1rem;
            color: white;
            opacity: 0.9;
            font-weight: 300;
            margin-bottom: 4px;
        }

        /* Progress Steps */
        .progress-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .progress-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            height: 4px;
            width: 100%;
            background-color: #ffe0b2;
            z-index: 1;
        }

        .progress-bar {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            height: 4px;
            width: 0%;
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            z-index: 2;
            transition: width 0.4s ease;
        }

        .step {
            background-color: white;
            color: #999;
            border-radius: 50%;
            height: 50px;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #ffe0b2;
            transition: all 0.4s ease;
            z-index: 3;
            position: relative;
        }

        .step.active {
            border-color: #FFA500;
            color: #FFA500;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(255, 165, 0, 0.2);
        }

        .step.completed {
            border-color: #FF8C00;
            background-color: #FF8C00;
            color: white;
        }

        .step-label {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 0.9rem;
            color: #b38f00;
        }

        .step.active .step-label {
            color: #FF8C00;
            font-weight: 600;
        }

        /* Form Steps */
        .form-steps {
            position: relative;
            background: white;
            box-shadow: 0 5px 20px rgba(255, 140, 0, 0.1);
            border: 1px solid #ffecb3;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease;
        }

        .form-step {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-step.active {
            display: block;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: #e65100;
            display: flex;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #fff3e0;
        }

        .section-title i {
            margin-right: 12px;
            color: #FF8C00;
            font-size: 1.8rem;
        }

        /* Group Selection Styles */
        .group-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .group-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border: 2px solid #fff3e0;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
            background: #fffaf0;
        }

        .group-option:hover {
            border-color: #FFA500;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.2);
        }

        .group-option.selected {
            border-color: #FF8C00;
            background: linear-gradient(135deg, #fffaf0, #ffebcd);
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.15);
        }

        .group-info {
            display: flex;
            flex-direction: column;
        }

        .group-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #e65100;
        }

        .group-price {
            font-weight: 700;
            color: #ff6f00;
            font-size: 1.3rem;
            margin-top: 5px;
        }

        .person-counter {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .counter-btn {
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(255, 140, 0, 0.3);
        }

        .counter-btn:hover {
            background: linear-gradient(135deg, #FF8C00, #FF7F00);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.4);
        }

        .counter-btn:disabled {
            background: #ffcc80;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .counter-display {
            font-size: 1.5rem;
            font-weight: bold;
            min-width: 50px;
            text-align: center;
            color: #e65100;
        }

        /* Hotel Selection Styles */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .option-card {
            border: 2px solid #fff3e0;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
            background: #fffaf0;
        }

        .option-card:hover {
            border-color: #FFA500;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(255, 165, 0, 0.2);
        }

        .option-card.selected {
            border-color: #FF8C00;
            background: linear-gradient(135deg, #fffaf0, #ffebcd);
            box-shadow: 0 6px 18px rgba(255, 140, 0, 0.2);
        }

        .option-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #e65100;
        }

        .option-price {
            color: #ff6f00;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }

        .option-description {
            color: #b38f00;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Food Selection Styles */
        .food-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .food-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 2px solid #fff3e0;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
            background: #fffaf0;
        }

        .food-option:hover {
            border-color: #FFA500;
            transform: translateY(-2px);
        }

        .food-option.selected {
            border-color: #FF8C00;
            background: linear-gradient(135deg, #fffaf0, #ffebcd);
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.15);
        }

        .food-option input {
            display: none;
        }

        .food-info {
            display: flex;
            flex-direction: column;
        }

        .food-name {
            font-weight: 600;
        }

        .food-price {
            color: #ff6f00;
            font-weight: bold;
            margin-top: 5px;
        }

        /* Included Items Styles */
        .included-items {
            margin-top: 20px;
        }

        .included-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background-color: #fffaf0;
            border: 1px solid #ffecb3;
        }

        .included-item input[type="checkbox"] {
            margin-right: 15px;
            transform: scale(1.3);
            accent-color: #FF8C00;
        }

        .item-details {
            flex-grow: 1;
        }

        .item-title {
            font-weight: 600;
            color: #e65100;
            margin-bottom: 5px;
        }

        .item-price {
            color: #ff6f00;
            font-weight: bold;
        }

        /* Summary Styles */
        .summary-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #ffd54f;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.8rem;
            font-weight: bold;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 3px solid #FF8C00;
            color: #e65100;
        }

        .book-btn {
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 1.3rem;
            border-radius: 50px;
            cursor: pointer;
            display: block;
            width: 90%;
            /* margin-top: 30px; */
            transition: all 0.3s;
            font-weight: bold;
            letter-spacing: 1px;
            box-shadow: 0 6px 20px rgba(255, 140, 0, 0.4);
        }

        .book-btn:hover {
            background: linear-gradient(135deg, #FF8C00, #FF7F00);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 140, 0, 0.5);
        }

        .price-highlight {
            color: #ff6f00;
            font-weight: bold;
        }

        .note {
            text-align: center;
            margin-top: 20px;
            color: #b38f00;
            font-style: italic;
        }

        /* Navigation Buttons */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .nav-btn {
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(255, 140, 0, 0.3);
        }

        .nav-btn:hover {
            background: linear-gradient(135deg, #FF8C00, #FF7F00);
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(255, 140, 0, 0.4);
        }

        .nav-btn.prev {
            background: #3796fb;
        }

        .nav-btn.prev:disabled {
            background: #3796fb61;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Success Message */
        .success-message {
            text-align: center;
            padding: 40px;
        }

        .success-icon {
            font-size: 5rem;
            color: #FF8C00;
            margin-bottom: 20px;
        }

        .success-title {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #e65100;
        }

        .success-text {
            font-size: 1.2rem;
            color: #b38f00;
            margin-bottom: 30px;
        }

        /* Toastr Customization */
        .toast-success {
            background-color: #FF8C00 !important;
        }

        .toast-title {
            font-weight: 600;
        }

        /* Floating Action Button */
        .float-action-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .float-action-button ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 10px;
            top: -95px;
        }

        .float-action-button li {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .float-action-button:hover li {
            opacity: 1;
            transform: translateY(0);
        }

        .wa-widget-send-button a,
        .change-language a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        a.buttons {
            box-shadow: 0 5px 11px -2px rgba(0, 0, 0, 0), 0 4px 12px -7px rgba(0, 0, 0, 0);
            background-color: #ffffff00;
        }

        .wa-widget-send-button a:hover,
        .change-language a:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 18px rgba(255, 140, 0, 0.6);
        }

        .buttons.main-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            /* background: linear-gradient(135deg, #FFA500, #FF8C00); */
            border-radius: 50%;
            box-shadow: 0 6px 20px rgba(255, 140, 0, 0.5);
            transition: all 0.3s ease;
        }

        .buttons.main-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(255, 140, 0, 0.7);
        }

        .share-icon,
        .whatsapp-icon {
            width: 30px;
            height: 30px;
            filter: brightness(0) invert(1);
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .option-card {
            border: 2px solid #fff3e0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
            background: #fffaf0;
            position: relative;
        }

        .option-card:hover {
            border-color: #FFA500;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 165, 0, 0.15);
        }

        .option-card.selected {
            border-color: #FF8C00;
            background: linear-gradient(135deg, #fffaf0, #ffebcd);
            box-shadow: 0 6px 18px rgba(255, 140, 0, 0.2);
        }

        .option-card.selected::before {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 10px;
            background: #FF8C00;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        /* ////// */
        .food-item-card {
            border: 2px solid #fff3e0;
            border-radius: 12px;
            padding: 15px;
            background: #fffaf0;
            margin-bottom: 15px;
            transition: all 0.3s;
            position: relative;
        }

        .food-item-card:hover {
            border-color: #FFA500;
            box-shadow: 0 4px 12px rgba(255, 165, 0, 0.1);
        }

        /* Food Header - Responsive Layout */
        .food-item-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        /* Food Image - Responsive */
        .food-image {
            flex-shrink: 0;
        }

        .food-img {
            width: 70px;
            height: 55px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Food Basic Info - Responsive */
        .food-basic-info {
            flex: 1;
            min-width: 120px;
        }

        .food-name {
            font-weight: 600;
            color: #e65100;
            font-size: 1rem;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .food-name-hindi {
            color: #b38f00;
            font-size: 0.85rem;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .food-price {
            color: #ff6f00;
            font-weight: bold;
            font-size: 1.1rem;
            line-height: 1.2;
        }

        /* Quantity Controls - Responsive */
        .food-quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .quantity-btn {
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.8rem;
        }

        .quantity-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #FF8C00, #FF7F00);
            transform: scale(1.05);
        }

        .quantity-btn:disabled {
            background: #ffcc80;
            cursor: not-allowed;
            transform: none;
        }

        .quantity-input {
            width: 50px;
            height: 32px;
            border: 2px solid #FFA500;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            color: #e65100;
            background: white;
            font-size: 0.9rem;
        }



        .view-details-btn {
            background: none;
            border: none;
            color: #FF8C00;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
            width: 100%;
            justify-content: center;
            padding: 8px;
            /* margin-top: 8px; */
        }

        .view-details-btn[aria-expanded="true"] {
            background: rgba(255, 140, 0, 0.1);
        }

        .view-details-btn[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        .view-details-btn i {
            transition: transform 0.3s ease;
        }

        /* Collapse Content - Responsive */
        .food-details-content {
            width: 100%;
            position: static !important;
            /* Remove absolute positioning */
        }

        .details-card {
            background: white;
            border-radius: 8px;
            /* padding: 0px; */
            border: 1px solid #ffecb3;
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
        }

        .details-card-div {
            padding: 15px;
        }

        .details-card h4 {
            color: #e65100;
            font-size: 1.1rem;
            margin-bottom: 12px;
            border-bottom: 2px solid #ffecb3;
            padding-bottom: 8px;
        }

        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-list li {
            padding: 8px 0;
            border-bottom: 1px solid #fff3e0;
            color: #b38f00;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .menu-list li:last-child {
            border-bottom: none;
        }

        .menu-list strong {
            color: #e65100;
        }

        /* Mobile First Responsive Design */
        @media (max-width: 576px) {
            .food-item-card {
                padding: 12px;
                margin-bottom: 12px;
            }

            .food-item-header {
                gap: 10px;
            }

            .food-img {
                width: 60px;
                height: 50px;
            }

            .food-basic-info {
                min-width: 100px;
            }

            .food-name {
                font-size: 0.95rem;
            }

            .food-name-hindi {
                font-size: 0.8rem;
            }

            .food-price {
                font-size: 1rem;
            }

            .food-quantity-controls {
                gap: 6px;
            }

            .quantity-btn {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }

            .quantity-input {
                width: 45px;
                height: 28px;
                font-size: 0.85rem;
            }

            .view-details-btn {
                font-size: 0.8rem;
                padding: 6px;
            }

            .details-card {
                padding: 12px;
                max-height: 250px;
            }

            .details-card h4 {
                font-size: 1rem;
            }

            .menu-list li {
                font-size: 0.8rem;
                padding: 6px 0;
            }
        }

        @media (max-width: 400px) {
            .food-item-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .food-basic-info {
                width: 100%;
            }

            .food-quantity-controls {
                width: 100%;
                justify-content: center;
                margin-top: 8px;
            }

            .food-img {
                width: 50px;
                height: 40px;
            }
        }

        /* Tablet and Desktop */
        @media (min-width: 768px) {
            .food-item-card {
                padding: 20px;
            }

            .food-img {
                width: 80px;
                height: 60px;
            }

            .food-name {
                font-size: 1.1rem;
            }

            .quantity-btn {
                width: 35px;
                height: 35px;
            }

            .quantity-input {
                width: 55px;
                height: 35px;
            }
        }

        /* Smooth Collapse Animation */
        .collapse:not(.show) {
            display: none;
        }

        .collapsing {
            height: 0;
            overflow: hidden;
            transition: height 0.35s ease;
        }

        /* Scrollbar Styling for Details Card */
        .details-card::-webkit-scrollbar {
            width: 6px;
        }

        .details-card::-webkit-scrollbar-track {
            background: #fff3e0;
            border-radius: 3px;
        }

        .details-card::-webkit-scrollbar-thumb {
            background: #FFA500;
            border-radius: 3px;
        }

        .details-card::-webkit-scrollbar-thumb:hover {
            background: #FF8C00;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>{{ $tourData['tour_name'] }}</h1>
            <p class="subtitle">Book your unforgettable journey through traditional and modern Japan</p>
        </header>

        <!-- Progress Steps -->
        <div class="form-steps">
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
                <div class="step active" data-step="1">
                    <span>1</span>
                    <div class="step-label">Group Size</div>
                </div>
                <div class="step" data-step="2">
                    <span>2</span>
                    <div class="step-label">Food</div>
                </div>
                <div class="step" data-step="3">
                    <span>3</span>
                    <div class="step-label">Hotel</div>
                </div>
                <div class="step" data-step="4">
                    <span>4</span>
                    <div class="step-label">Summary</div>
                </div>
            </div>
            <!-- Step 1: Group Selection -->
            <div class="form-step active" id="step-1">
                <h2 class="section-title">
                    <i class="fas fa-users"></i> Group Size Selection
                </h2>
                <div class="group-options">
                    <div class="group-option" data-group="1" data-price="8000" data-min="1" data-max="1">
                        <div class="group-info">
                            <div class="group-name">Group of 1 (Per Person)</div>
                            <div class="group-price">¥8,000.00</div>
                        </div>
                        <div class="person-counter">
                            <button class="counter-btn decrease-btn" disabled>-</button>
                            <div class="counter-display">0</div>
                            <button class="counter-btn increase-btn">+</button>
                        </div>
                    </div>

                    <div class="group-option selected" data-group="2" data-price="7000" data-min="2" data-max="2">
                        <div class="group-info">
                            <div class="group-name">Group of 2 (Per Person)</div>
                            <div class="group-price">¥7,000.00</div>
                        </div>
                        <div class="person-counter">
                            <button class="counter-btn decrease-btn">-</button>
                            <div class="counter-display">2</div>
                            <button class="counter-btn increase-btn">+</button>
                        </div>
                    </div>

                    <div class="group-option" data-group="3" data-price="6000" data-min="3" data-max="3">
                        <div class="group-info">
                            <div class="group-name">Group of 3 (Per Person)</div>
                            <div class="group-price">¥6,000.00</div>
                        </div>
                        <div class="person-counter">
                            <button class="counter-btn decrease-btn">-</button>
                            <div class="counter-display">0</div>
                            <button class="counter-btn increase-btn">+</button>
                        </div>
                    </div>

                    <div class="group-option" data-group="4-10" data-price="5000" data-min="4" data-max="10">
                        <div class="group-info">
                            <div class="group-name">Group of 4 - 10 (Per Person)</div>
                            <div class="group-price">¥5,000.00</div>
                        </div>
                        <div class="person-counter">
                            <button class="counter-btn decrease-btn">-</button>
                            <div class="counter-display">0</div>
                            <button class="counter-btn increase-btn">+</button>
                        </div>
                    </div>

                    <div class="group-option" data-group="11-45" data-price="4500" data-min="11" data-max="45">
                        <div class="group-info">
                            <div class="group-name">Group of 11 - 45 (Per Person)</div>
                            <div class="group-price">¥4,500.00</div>
                        </div>
                        <div class="person-counter">
                            <button class="counter-btn decrease-btn">-</button>
                            <div class="counter-display">0</div>
                            <button class="counter-btn increase-btn">+</button>
                        </div>
                    </div>
                </div>

                <div class="form-navigation">
                    <button class="nav-btn prev" disabled>
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button class="nav-btn next" id="next-1">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>


            <!-- Step 2: Food Selection -->
            <div class="form-step" id="step-2">
                <h2 class="section-title">
                    <i class="fas fa-utensils"></i> Food Preferences
                </h2>
                <div class="row">
                    @for($i = 0; $i < 5; $i++)
                        <div class="col-12 food-item-card">
                        <div class="food-item-header">
                            <div class="food-image">
                                <img src="https://sit.rizrv.com/public/assets/back-end/img/placeholder/product.png"
                                    alt="Food Item" class="food-img">
                            </div>
                            <div class="food-basic-info">
                                <div class="food-name">Breakfast Thali</div>
                                <div class="food-name-hindi">थाली</div>
                                <div class="food-price">₹100.00</div>
                            </div>
                            <div class="food-quantity-controls">
                                <button class="quantity-btn minus" onclick="updateFoodQuantity(this, -1)" data-price="100">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="0" min="0" max="10" readonly
                                    data-price="100" data-name="Breakfast Thali">
                                <button class="quantity-btn plus" onclick="updateFoodQuantity(this, 1)" data-price="100">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="details-card">
                            <button class="view-details-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#foodDetails{{$i}}" aria-expanded="false"
                                aria-controls="foodDetails{{$i}}">
                                <i class="fas fa-chevron-down"></i> View Details
                            </button>

                            <div class="collapse food-details-content" id="foodDetails{{$i}}">
                                <div class="details-card-div">
                                    <h4>Menu Includes:</h4>
                                    <ul class="menu-list">
                                        <li><strong>1. साधारण डिनर थाली</strong> – रोटी, चावल, दाल, मौसमी सब्जी, सलाद, दही, अचार</li>
                                        <li><strong>2. शाही डिनर थाली</strong> – बटर नान, जीरा राइस, पनीर बटर मसाला, दाल मखनी, मिक्स वेज, रायता, सलाद, गुलाब जामुन</li>
                                        <li><strong>3. गुजराती डिनर थाली</strong> – फुलका, खिचड़ी, कढ़ी, सेव टमाटर सब्जी, थेपला, छाछ, बासुंदी</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                </div>
                @endfor
            </div>            
             <div class="form-navigation">
                <button class="nav-btn prev" id="prev-2">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button class="nav-btn next" id="next-2">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
        <!-- Step 3: Hotel Selection -->
        <div class="form-step" id="step-3">
            <h2 class="section-title">
                <i class="fas fa-hotel"></i> Select Hotel Option
            </h2>
            <div class="row">
                <?php for ($i = 0; $i < 5; $i++) { ?>
                    <div class="col-12 food-item-card">
                        <div class="food-item-header">
                            <div class="food-image">
                                <img src="https://sit.rizrv.com/public/assets/back-end/img/placeholder/product.png" alt="Food Item" class="food-img">
                            </div>
                            <div class="food-basic-info">
                                <div class="food-name">{{$i}} star hotel</div>
                                <div class="food-price">₹100.00</div>
                            </div>
                            <div class="food-quantity-controls">
                                <button class="quantity-btn minus" onclick="updateFoodQuantity(this, -1)" data-price="100">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="0" min="0" max="10" readonly data-price="100" data-name="Breakfast Thali">
                                <button class="quantity-btn plus" onclick="updateFoodQuantity(this, 1)" data-price="100">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="details-card">
                            <button class="view-details-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#foodDetails{{$i}}" aria-expanded="false"
                                aria-controls="foodDetails{{$i}}">
                                <i class="fas fa-chevron-down"></i> View Details
                            </button>

                            <div class="collapse food-details-content" id="foodDetails{{$i}}">
                                <div class="details-card-div">
                                    <h4>Menu Includes:</h4>
                                    <ul class="menu-list">
                                        <li><strong>1. साधारण डिनर थाली</strong> – रोटी, चावल, दाल, मौसमी सब्जी, सलाद, दही, अचार</li>
                                        <li><strong>2. शाही डिनर थाली</strong> – बटर नान, जीरा राइस, पनीर बटर मसाला, दाल मखनी, मिक्स वेज, रायता, सलाद, गुलाब जामुन</li>
                                        <li><strong>3. गुजराती डिनर थाली</strong> – फुलका, खिचड़ी, कढ़ी, सेव टमाटर सब्जी, थेपला, छाछ, बासुंदी</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="form-navigation">
                <button class="nav-btn prev" id="prev-3">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button class="nav-btn next" id="next-3">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 5: Summary -->
        <div class="form-step" id="step-4">
            <h2 class="section-title">
                <i class="fas fa-file-invoice-dollar"></i> Booking Summary
            </h2>
            <div class="summary-details">
                <div>
                    <div class="summary-item">
                        <span>Tour Price (x<span id="person-count">2</span> people):</span>
                        <span class="price-highlight" id="tour-price">¥14,000</span>
                    </div>
                    <div class="summary-item">
                        <span>Hotel (3 nights):</span>
                        <span class="price-highlight" id="hotel-price">¥24,000</span>
                    </div>
                    <div class="summary-item">
                        <span>Food (3 days):</span>
                        <span class="price-highlight" id="food-price">¥13,500</span>
                    </div>
                </div>
                <div>
                    <div class="summary-item">
                        <span>Included Items:</span>
                        <span class="price-highlight" id="included-price">¥3,100</span>
                    </div>
                    <div class="summary-item">
                        <span>Taxes & Fees:</span>
                        <span class="price-highlight" id="taxes-price">¥5,100</span>
                    </div>
                    <div class="summary-item">
                        <span>Discount:</span>
                        <span class="price-highlight" id="discount-price">-¥2,000</span>
                    </div>
                </div>
            </div>
            <div class="summary-total">
                <span>Total Amount:</span>
                <span id="total-price">¥54,600</span>
            </div>
            <p class="note">Prices are in Japanese Yen (¥). 3-day tour with 3-night hotel stay included.</p>

            <div class="form-navigation">
                <button class="nav-btn prev" id="prev-5">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button class="book-btn" id="complete-booking">
                    Complete Booking
                </button>
            </div>
        </div>

        <!-- Success Message -->
        <div class="form-step" id="step-success">
            <div class="success-message">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="success-title">Booking Confirmed!</h2>
                <p class="success-text">Thank you for your booking. We've sent a confirmation email with all the details.</p>
                <button class="nav-btn" id="new-booking">
                    <i class="fas fa-plus"></i> New Booking
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Floating Action Button -->
    <div class="float-action-button">
        <!-- Dropdown menu -->
        <ul>
            @foreach (json_decode($language['value'], true) as $key => $data)
            @if ($data['status'] == 1)
            <li class="change-language changeLanguage" data-action="{{ route('change-language') }}"
                data-language-code="{{ $data['code'] }}">
                <a href="javascript:" class="buttons" title="{{ $data['name'] }}" data-toggle="tooltip"
                    data-placement="left">
                    <img class="mr-2" width="20"
                        src="{{ theme_asset('public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
                        alt="{{ $data['name'] }}">
                </a>
            </li>
            @endif
            @endforeach
        </ul>

        <!-- Main button -->
        @foreach (json_decode($language['value'], true) as $data)
        @if ($data['code'] == getDefaultLanguage())
        <a href="javascript:" class="buttons main-button" title="{{ $data['name'] }}"
            data-toggle="tooltip" data-placement="left">
            <img class="mr-2" width="20"
                src="{{ theme_asset('public/assets/front-end/img/menu-icon/iconLanguage.png') }}"
                alt="{{ $data['name'] }}">
        </a>
        @endif
        @endforeach
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Configure Toastr with yellow/orange theme
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Step Navigation
        const steps = document.querySelectorAll('.form-step');
        const progressBar = document.getElementById('progress-bar');
        const stepIndicators = document.querySelectorAll('.step');
        let currentStep = 1;

        // Initialize navigation
        function initNavigation() {
            // Next buttons
            document.getElementById('next-1').addEventListener('click', () => navigateToStep(2));
            document.getElementById('next-2').addEventListener('click', () => navigateToStep(3));
            document.getElementById('next-3').addEventListener('click', () => navigateToStep(4));

            // Previous buttons
            document.getElementById('prev-2').addEventListener('click', () => navigateToStep(1));
            document.getElementById('prev-3').addEventListener('click', () => navigateToStep(2));
            document.getElementById('prev-5').addEventListener('click', () => navigateToStep(3));

            // Complete booking
            document.getElementById('complete-booking').addEventListener('click', completeBooking);
            document.getElementById('new-booking').addEventListener('click', resetForm);
        }

        // Navigate to specific step
        function navigateToStep(step) {
            // Hide all steps
            steps.forEach(s => s.classList.remove('active'));

            // Show target step
            document.getElementById(`step-${step}`).classList.add('active');

            // Update progress bar
            const progressPercentage = ((step - 1) / (steps.length - 2)) * 100;
            progressBar.style.width = `${progressPercentage}%`;

            // Update step indicators
            stepIndicators.forEach((indicator, index) => {
                if (index + 1 < step) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                } else if (index + 1 === step) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            });

            currentStep = step;
            updateSummary();
        }

        // Complete booking
        function completeBooking() {
            // Hide all steps
            steps.forEach(s => s.classList.remove('active'));

            // Show success message
            document.getElementById('step-success').classList.add('active');

            // Update progress to 100%
            progressBar.style.width = '100%';

            // Mark all steps as completed
            stepIndicators.forEach(indicator => {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            });

            // Show success toast
            toastr.success('Your tour has been booked successfully!');
        }

        // Reset form
        function resetForm() {
            // Reset to first step
            navigateToStep(1);

            // Reset form selections
            const groupOptions = document.querySelectorAll('.group-option');
            groupOptions.forEach(option => {
                option.classList.remove('selected');
                const display = option.querySelector('.counter-display');
                const min = parseInt(option.getAttribute('data-min'));
                display.textContent = min > 0 ? min : 0;
                updateCounterButtons(option);
            });

            // Select default group
            document.querySelector('.group-option[data-group="2"]').classList.add('selected');

            // Reset food selection
            const foodOptions = document.querySelectorAll('.food-option');
            foodOptions.forEach(option => {
                const checkbox = option.querySelector('input');
                checkbox.checked = option.querySelector('input[value="breakfast"]') || option.querySelector('input[value="dinner"]');
                option.classList.toggle('selected', checkbox.checked);
            });

            updateSummary();

            toastr.info('Form has been reset. You can start a new booking.');
        }

        // Group selection functionality
        const groupOptions = document.querySelectorAll('.group-option');
        let selectedGroup = document.querySelector('.group-option.selected');

        groupOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                // Don't trigger group selection if clicking counter buttons
                if (e.target.classList.contains('counter-btn')) return;

                // Remove selected class from all options
                groupOptions.forEach(opt => {
                    opt.classList.remove('selected');
                    // Reset counters for non-selected groups
                    if (opt !== option) {
                        const display = opt.querySelector('.counter-display');
                        const min = parseInt(opt.getAttribute('data-min'));
                        display.textContent = min > 0 ? min : 0;

                        // Update button states
                        updateCounterButtons(opt);
                    }
                });

                // Add selected class to clicked option
                option.classList.add('selected');
                selectedGroup = option;

                // Set counter to minimum for selected group
                const min = parseInt(option.getAttribute('data-min'));
                const display = option.querySelector('.counter-display');
                display.textContent = min;

                // Update button states
                updateCounterButtons(option);

                updateSummary();
            });
        });

        const decreaseBtns = document.querySelectorAll('.decrease-btn');
        const increaseBtns = document.querySelectorAll('.increase-btn');

        decreaseBtns.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const option = e.target.closest('.group-option');
                if (!option.classList.contains('selected')) return;

                const display = option.querySelector('.counter-display');
                let count = parseInt(display.textContent);
                const min = parseInt(option.getAttribute('data-min'));

                if (count > min) {
                    count--;
                    display.textContent = count;
                    updateCounterButtons(option);
                    updateSummary();
                }
            });
        });

        increaseBtns.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const option = e.target.closest('.group-option');
                if (!option.classList.contains('selected')) return;

                const display = option.querySelector('.counter-display');
                let count = parseInt(display.textContent);
                const max = parseInt(option.getAttribute('data-max'));

                if (count < max) {
                    count++;
                    display.textContent = count;
                    updateCounterButtons(option);
                    updateSummary();
                }
            });
        });

        // Update counter button states
        function updateCounterButtons(option) {
            const display = option.querySelector('.counter-display');
            const count = parseInt(display.textContent);
            const min = parseInt(option.getAttribute('data-min'));
            const max = parseInt(option.getAttribute('data-max'));

            const decreaseBtn = option.querySelector('.decrease-btn');
            const increaseBtn = option.querySelector('.increase-btn');

            decreaseBtn.disabled = count <= min;
            increaseBtn.disabled = count >= max;
        }


        // Food selection functionality
        const foodOptions = document.querySelectorAll('.food-option');

        foodOptions.forEach(option => {
            option.addEventListener('click', () => {
                const checkbox = option.querySelector('input');
                checkbox.checked = !checkbox.checked;
                option.classList.toggle('selected', checkbox.checked);

                updateSummary();
            });
        });

        // Update summary function
        function updateSummary() {
            // Get selected group and person count
            const groupPrice = parseInt(selectedGroup.getAttribute('data-price'));
            const personCount = parseInt(selectedGroup.querySelector('.counter-display').textContent);
            const tourPrice = groupPrice * personCount;

            // Get hotel price
            const hotelPricePerNight = parseInt(selectedHotel.getAttribute('data-price'));
            const hotelNights = 3; // Fixed for this example
            const totalHotelPrice = hotelPricePerNight * hotelNights * personCount;

            // Get food price
            let foodPricePerDay = 0;
            const foodCheckboxes = document.querySelectorAll('input[name="food"]:checked');
            foodCheckboxes.forEach(checkbox => {
                foodPricePerDay += parseInt(checkbox.getAttribute('data-price'));
            });
            const foodDays = 3; // Fixed for this example
            const totalFoodPrice = foodPricePerDay * foodDays * personCount;

            // Included items price (fixed)
            const includedPrice = 3100;

            // Calculate other costs
            const taxes = Math.round((tourPrice + totalHotelPrice + totalFoodPrice + includedPrice) * 0.1);
            const discount = personCount >= 4 ? 2000 : 0;

            // Calculate total
            const total = tourPrice + totalHotelPrice + totalFoodPrice + includedPrice + taxes - discount;

            // Update display
            document.getElementById('person-count').textContent = personCount;
            document.getElementById('tour-price').textContent = `¥${tourPrice.toLocaleString()}`;
            document.getElementById('hotel-price').textContent = `¥${totalHotelPrice.toLocaleString()}`;
            document.getElementById('food-price').textContent = `¥${totalFoodPrice.toLocaleString()}`;
            document.getElementById('included-price').textContent = `¥${includedPrice.toLocaleString()}`;
            document.getElementById('taxes-price').textContent = `¥${taxes.toLocaleString()}`;
            document.getElementById('discount-price').textContent = `-¥${discount.toLocaleString()}`;
            document.getElementById('total-price').textContent = `¥${total.toLocaleString()}`;
        }

        // Initialize
        initNavigation();
        updateCounterButtons(selectedGroup);
        updateSummary();
    </script>

    <script>
        $(".change-language").on("click", function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
            });
            $.ajax({
                type: "POST",
                url: $(this).data("action"),
                data: {
                    language_code: $(this).data("language-code"),
                },
                success: function(data) {
                    toastr.success(data.message);
                    location.reload();
                },
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all collapse elements
            const collapseElements = document.querySelectorAll('.collapse');

            // Add event listeners for collapse show/hide
            const viewButtons = document.querySelectorAll('.view-details-btn');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-bs-target');
                    const collapseElement = document.querySelector(target);

                    const id_show = this.getAttribute('aria-controls');
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);
                    if (isExpanded) {
                        $(`#${id_show}`).addClass('hide');
                        $(`#${id_show}`).removeClass('show');
                    } else {
                        $(`#${id_show}`).removeClass('hide');
                        $(`#${id_show}`).addClass('show');
                    }

                    // Toggle icon rotation
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                });
            });
        });
    </script>
</body>

</html>