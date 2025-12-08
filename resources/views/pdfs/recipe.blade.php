<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $recipe->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            border-bottom: 3px solid #FF6B35;
        }

        h1 {
            font-size: 32px;
            color: #FF6B35;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .recipe-meta {
            display: table;
            width: 100%;
            margin: 20px 0;
            background: #FFF5E9;
            padding: 15px;
            border-radius: 8px;
        }

        .meta-item {
            display: inline-block;
            width: 48%;
            margin: 5px 0;
            font-size: 14px;
        }

        .meta-label {
            font-weight: bold;
            color: #FF6B35;
        }

        .section {
            margin: 25px 0;
        }

        h2 {
            font-size: 20px;
            color: #FF6B35;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #FFB84D;
        }

        .description {
            padding: 15px;
            background: #FFEBD6;
            border-left: 4px solid #FF6B35;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .rating {
            margin: 15px 0;
            font-size: 16px;
        }

        .stars {
            color: #FFB84D;
            font-size: 18px;
            letter-spacing: 2px;
        }

        .ingredients-list {
            list-style: none;
            padding: 0;
        }

        .ingredients-list li {
            padding: 8px 12px;
            margin: 5px 0;
            background: #FFF5E9;
            border-left: 3px solid #FFB84D;
            font-size: 14px;
        }

        .ingredient-quantity {
            font-weight: bold;
            color: #FF6B35;
        }

        .instructions-list {
            list-style: none;
            counter-reset: step-counter;
            padding: 0;
        }

        .instructions-list li {
            counter-increment: step-counter;
            padding: 12px 15px 12px 45px;
            margin: 10px 0;
            background: #FFF5E9;
            border-radius: 5px;
            position: relative;
            font-size: 14px;
        }

        .instructions-list li:before {
            content: counter(step-counter);
            position: absolute;
            left: 12px;
            top: 12px;
            background: transparent;
            color: #FF6B35;
            font-weight: bold;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            text-align: center;
            line-height: 24px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #FFB84D;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .logo {
            font-size: 16px;
            font-weight: bold;
            background: linear-gradient(135deg, #FF6B35, #E63946);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">FOODYML</div>
        <h1>{{ $recipe->name }}</h1>

        @if($recipe->average_rating > 0)
        <div class="rating">
            <span class="stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($recipe->average_rating))
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </span>
            <span>{{ number_format($recipe->average_rating, 1) }} / 5.0</span>
        </div>
        @endif
    </div>

    <div class="recipe-meta">
        <div class="meta-item">
            <span class="meta-label">Gatavošanas laiks:</span> {{ $recipe->cooking_time }} min
        </div>
        <div class="meta-item">
            <span class="meta-label">Grūtības pakāpe:</span> {{ $recipe->difficulty }}
        </div>
        <div class="meta-item">
            <span class="meta-label">Ēdienreize:</span> {{ $recipe->meal_time }}
        </div>
        <div class="meta-item">
            <span class="meta-label">Uzturs:</span> {{ $recipe->nutrition }}
        </div>
        @if($recipe->diet_type)
        <div class="meta-item">
            <span class="meta-label">Diētas tips:</span> {{ $recipe->diet_type }}
        </div>
        @endif
        @if($recipe->protein_source)
        <div class="meta-item">
            <span class="meta-label">Olbaltumvielu avots:</span> {{ $recipe->protein_source }}
        </div>
        @endif
    </div>

    @if($recipe->description)
    <div class="section">
        <h2>Apraksts</h2>
        <div class="description">
            {{ $recipe->description }}
        </div>
    </div>
    @endif

    <div class="section">
        <h2>Sastāvdaļas</h2>
        <ul class="ingredients-list">
            @forelse($recipe->ingredients as $ingredient)
            <li>
                <span class="ingredient-quantity">
                    {{ $ingredient->pivot->quantity }}
                </span>
                {{ $ingredient->name }}
                @if($ingredient->pivot->notes)
                    <em>({{ $ingredient->pivot->notes }})</em>
                @endif
            </li>
            @empty
            <li>Nav sastāvdaļu</li>
            @endforelse
        </ul>
    </div>

    <div class="section">
        <h2>Gatavošanas soļi</h2>
        <ol class="instructions-list">
            @forelse($recipe->instructions->sortBy('step_number') as $instruction)
            <li>{{ $instruction->description }}</li>
            @empty
            <li>Nav gatavošanas instrukciju</li>
            @endforelse
        </ol>
    </div>

    <div class="footer">
        <p>PDF ģenerēts ar FOODYML • {{ now()->format('Y-m-d H:i') }}</p>
        <p>Šī recepte ir paredzēta personīgai lietošanai</p>
    </div>
</body>
</html>
