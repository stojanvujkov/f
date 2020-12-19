<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                font-family: Verdana, "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 12px;
            }
            .container {
                margin: 0 auto 0 auto;
                max-width: 980px;
            }
            h1 {
                font-size: 32px;
                margin-bottom: 5px;
            }
            a {
                color: #000;
            }
            table td {
                vertical-align: top;
            }
            .correspondents {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 10px;
            }
            .line_items {
                border-collapse: collapse;
                width: 100%;
            }
            .line_items thead {
                background-color: #333;
                color: #fff;
            }
            .line_items tbody,
            .line_items td {
                border: 1px solid #ddd;
            }
            .line_items thead th,
            .line_items tbody td {
                padding: 10px;
            }
            .item_image {
                text-align: left;
                width: 20%;
            }
            .item_image img {
                width: 100%;
                height: auto;
            }
            .item_sku {
                text-align: left;
                overflow-wrap: break-word;
                word-break: break-all;
            }
            .item_product {
                text-align: left;
                overflow-wrap: break-word;
                word-break: break-all;
            }
            .item_quantity {
                text-align: center;
                width: 10%;
            }
            .item_price {
                text-align: right;
                width: 10%;
            }
            .order_detail {
                width: 100%;
                border-collapse: collapse;
            }
            .order_detail tr {
                border-top: 1px solid #ddd;
                border-bottom: 1px solid #ddd;
            }
            .order_detail tr:first-child,
            .order_detail tr:last-child {
                border: none;
            }
            .order_detail td {
                padding: 7px 5px 7px 5px;
            }
            .detail_label {
                font-weight: bold;
                text-align: right;
            }
            .detail_value {
                text-align: right;
                width: 10%;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <h1>
                Frame Shops
            </h1>
            <h2 style="margin-bottom: 5px;">
                <?php echo $invoice; ?>
            </h2>
            Order date:
            <?php
                $date_created = new DateTime($e['date_created']);
                echo $date_created->format('F d, Y');
            ?><br>
            <table class="correspondents">
                <tbody>
                <tr>
                    <td>
                        <h2>Billing Address</h2>
                        <?php echo $billing_address; ?>
                    </td>
                    <td>
                        <h2>Shipping Address</h2>
                        <?php echo $shipping_address; ?>
                    </td>
                    <td>
                        <h2>Shipping Method</h2>
                        <?php echo $shipping_method; ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="line_items">
                <thead>
                    <th class="item_image"></th>
                    <th class="item_sku">SKU</th>
                    <th class="item_product">Product</th>
                    <th class="item_quantity">Quantity</th>
                    <th class="item_price">Price</th>
                </thead>
                <tbody>
                <?php
                    $lines = [];
                    if (isset($e['line_items']) && is_array($e['line_items'])) {
                        foreach ($e['line_items'] as $item) {
                            ?>
                            <tr>
                                <td class="item_image"><img src="<?php echo retrieve($item['sku']) . '/wall_cart.jpg'; ?>"><?php echo ''; ?></td>
                                <td class="item_sku"><?php echo retrieve($item['sku']); ?></td>
                                <td class="item_product">
                                    <span style="text-decoration: underline;"><?php echo retrieve($item['name']) . "<br>\n"; ?></span>
                                    <?php
                                        if (isset($item['meta_data']) && is_array($item['meta_data'])) {
                                            foreach ($item['meta_data'] as $data) {
                                                if ($data['key'] == 'Frame number') {
                                                    echo 'Frame number:' . $data['value'] . "<br>\n";
                                                }
                                                if ($data['key'] == 'description') {
                                                    echo $data['value'];
                                                }
                                            }
                                        }
                                    ?>
                                    <br>
                                    <a href="https://frameshops.com/gallery-walls/1x3" target="_blank">https://frameshops.com/gallery-walls/1x3</a>
                                </td>
                                <td class="item_quantity"><?php echo retrieve($item['quantity']); ?></td>
                                <td class="item_price"><?php echo retrieve($e['currency_symbol']) . retrieve($item['total']); ?></td>
                            </tr>
                            <?php
                        }
                    }
                ?>
                </tbody>
            </table>
            <table class="order_detail">
                <tr>
                    <td class="detail_label">Subtotal: </td>
                    <td class="detail_value"><?php echo retrieve($e['currency_symbol']) . number_format($subtotal, 2, '.', ''); ?></td>
                </tr>
                <tr>
                    <td class="detail_label">Shipping: </td>
                    <td class="detail_value"><?php echo retrieve($e['shipping_lines'][0]['method_title']) . retrieve($e['']); ?></td>
                </tr>
                <tr>
                    <td class="detail_label">Sales tax: </td>
                    <td class="detail_value"><?php echo retrieve($e['currency_symbol']) . retrieve($e['total_tax']); ?></td>
                </tr>
                <tr>
                    <td class="detail_label">Payment method: </td>
                    <td class="detail_value"><?php echo retrieve($e['payment_method_title']); ?></td>
                </tr>
                <tr>
                    <td class="detail_label">Total: </td>
                    <td class="detail_value"><?php echo retrieve($e['currency_symbol']) . retrieve($e['total']); ?></td>
                </tr>
            </table>
        </div>
    </body>
</html>