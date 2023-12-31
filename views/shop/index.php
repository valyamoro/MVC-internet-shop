<?php

if (empty($products)) {
    echo 'Файл с продуктами пуст';
    die;
} else {
    foreach ($productsOnPage as $key => $value) {
        ?>
        <div class="product-item">
            <form method="post" action="product.php?action=add&code=<?php echo $value[1]; ?>">
                <div class="product-tile-footer">
                    <div class="product-id"><?php echo "Айди: {$value['id']}" ?></div>
                    <div class="product-title"><?php echo "Название: {$value['title']}" ?></div>
                    <div class="product-count"><?php echo "Количество: {$value['quantity']}" ?></div>
                    <div class="product-price"><?php echo "Цена: {$value['price']}" ?></div>
                </div>
            </form>
            <?php echo '-------------------------------' ?>
        </div>
        <?php
    }
}
?>

<?php if ($totalPages > 1): // Проверяем, есть ли больше одной страницы для пагинации ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo ($currentPage - 1); ?>">Предыдущая страница</a>
        <?php endif ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $currentPage): ?>
                <span><?php echo $i; ?></span>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif ?>
        <?php endfor ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo ($currentPage + 1); ?>">Следующая страница</a>
        <?php endif ?>
    </div>
<?php endif; ?>