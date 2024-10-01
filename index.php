<?php
require_once 'App/Infrastructure/DataAdapter.php';
use App\Infrastructure\DataAdapter; 

$dataAdapter = new DataAdapter();
?>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="assets/css/style.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="row row-header">
        <div class="col-12" id="count">
            <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
            <h1>Прокат Y</h1>
        </div>
    </div>

    <div class="row row-form">
        <div class="col-12">
            <form action="App/calculate.php" method="POST" id="form">

                <?php $products = $dataAdapter->getProducts();
                if (is_array($products)) { ?>
                    <label class="form-label" for="product">Выберите продукт:</label>
                    <select class="form-select" name="product" id="product">
                        <?php foreach ($products as $product) {
                            $name = $product['NAME'];
                            ?>
                            <option value="<?= $product['ID']; ?>"><?= $name; ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>

                <div id="tariff-display" style="margin-top: 5px;"></div>

                <label for="customRange1" class="form-label" id="count">Количество дней:</label>
                <input type="number" name="days" class="form-control" id="customRange1" min="1" max="30">

                <?php $services = $dataAdapter->getServices();
                if (is_array($services)) { ?>
                    <label for="customRange1" class="form-label">Дополнительно:</label>
                    <?php
                    $index = 0;
                    foreach ($services as $k => $s) {
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="<?= $s; ?>" id="flexCheck<?= $index; ?>">
                            <label class="form-check-label" for="flexCheck<?= $index; ?>">
                                <?= $k ?>: <?= $s ?>
                            </label>
                        </div>
                    <?php $index++; } ?>
                <?php } ?>
                <button type="submit" class="btn btn-primary">Рассчитать</button>
            </form>

            <h5>Итоговая стоимость: <span id="total-price"></span></h5>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Обработка отправки формы
        $("#form").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: 'App/calculate.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $("#total-price").text(response);
                },
                error: function() {
                    $("#total-price").text('Ошибка при расчете');
                }
            });
        });

        function fetchTariffs() {
        var productId = $("#product").val();
            $.ajax({
                url: 'App/Infrastructure/getTariff.php',
                type: 'POST',
                data: { productId: productId },
                success: function(response) {
                    var tariffDisplay = $("#tariff-display");
                    tariffDisplay.empty();
                    if (response) {
                        try {
                            if (response.message) {
                                tariffDisplay.text('Нет тарифа'); 
                            } else {
                                var tariffs = parseSerializedArray(response);
                                if (tariffs.length === 0) {
                                    tariffDisplay.text('Нет тарифа');
                                } else {
                                    var table = '<div>Тариф</div><table class="table"><thead><tr><th>Количество дней</th><th>Стоимость в день</th></tr></thead><tbody>';
                                    tariffs.forEach(function(tariff) {
                                        table += '<tr><td>' + tariff.days + '</td><td>' + tariff.cost + '</td></tr>';
                                    });
                                    table += '</tbody></table>';
                                    tariffDisplay.html(table);
                                }
                            }
                        } catch (error) {
                            tariffDisplay.text('Ошибка обработки данных');
                        }
                    } else {
                        tariffDisplay.text('Неизвестная ошибка');
                    }
                },
                error: function() {
                    $("#tariff-display").text('Ошибка при получении тарифов');
                }
            });
        }

        fetchTariffs();

        $("#product").change(function() {
            fetchTariffs();
        });

        
        function parseSerializedArray(serialized) {
            var result = [];
            var matches = serialized.match(/i:(\d+);i:(\d+);/g);
            
            if (matches) {
                for (var i = 0; i < matches.length; i += 1) {
                    var l_matches = matches[i].match(/i:(\d+);/g);
                    var days = parseInt(l_matches[0].match(/i:(\d+);/)[1]);
                    var cost = parseInt(l_matches[1].match(/i:(\d+);/)[1]);
                    result.push({ days: days, cost: cost });
                }
            }

            result.sort((a, b) => a.days - b.days);

            var formattedResult = [];
            for (var i = 0; i < result.length; i++) {
                var daysRange = i < result.length - 1 
                    ? `от ${result[i].days} до ${result[i + 1].days}` 
                    : `от ${result[i].days}`; 
                formattedResult.push({ days: daysRange, cost: result[i].cost });
            }
            return formattedResult;
        }

    });
</script>
</body>
</html>
