<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Iepirkumu saraksts</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 40px;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .logo {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        h1 {
            font-size: 26px;
            color: #000;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .meta {
            font-size: 12px;
            color: #555;
        }

        .recipes-section {
            margin: 20px 0;
            padding: 10px 14px;
            border-left: 3px solid #000;
        }

        .recipes-section h3 {
            font-size: 13px;
            color: #000;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .recipe-tag {
            display: inline-block;
            font-size: 12px;
            color: #333;
            margin-right: 12px;
        }

        .recipe-tag .portions {
            font-weight: bold;
        }

        .category-block {
            margin: 20px 0;
        }

        .category-title {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ingredient-row {
            display: table;
            width: 100%;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        .ingredient-check {
            display: table-cell;
            width: 20px;
            vertical-align: middle;
            color: #555;
            font-size: 14px;
        }

        .ingredient-qty {
            display: table-cell;
            width: 120px;
            font-weight: bold;
            color: #000;
            vertical-align: middle;
        }

        .ingredient-name {
            display: table-cell;
            vertical-align: middle;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #999;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        @page { margin: 2cm; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">FOODYML</div>
    <h1>Iepirkumu saraksts</h1>
    <div class="meta">Ģenerēts: {{ $generatedAt }}</div>
</div>

<div class="recipes-section">
    <h3>Iekļautās receptes:</h3>
    @foreach($recipeNames as $r)
        <span class="recipe-tag">
            {{ $r['name'] }} <span class="portions">({{ $r['portions'] }}x)</span>
        </span>
    @endforeach
</div>

@foreach($list as $group)
<div class="category-block">
    <div class="category-title">{{ $group['category'] }}</div>
    @foreach($group['ingredients'] as $ingredient)
    <div class="ingredient-row">
        <span class="ingredient-check">☐</span>
        <span class="ingredient-qty">{{ $ingredient['quantity'] }} {{ $ingredient['unit'] }}</span>
        <span class="ingredient-name">{{ $ingredient['name'] }}</span>
    </div>
    @endforeach
</div>
@endforeach

<div class="footer">
    <p>FOODYML &bull; Iepirkumu saraksts &bull; {{ $generatedAt }}</p>
</div>

</body>
</html>
