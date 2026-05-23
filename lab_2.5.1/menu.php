<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

function menuLink($linkAction, $text, $currentAction, $extra = '') {
    $active = ($linkAction == $currentAction) ? 'select' : '';
    return "<a href='?action=$linkAction$extra' class='$active'>$text</a>";
}
?>
<aside>
    <div class="submenu">
        <?php
        echo menuLink('view', 'Просмотр', $action);
        echo menuLink('add', 'Добавление записи', $action);
        echo menuLink('edit', 'Редактирование записи', $action);
        echo menuLink('delete', 'Удаление записи', $action);
        ?>
    </div>

    <?php if ($action == 'view'): ?>
    <div class="submenu sort-menu">
        <?php
        $sorts = [
            'id' => 'По добавлению',
            'surname' => 'По фамилии',
            'birth_date' => 'По дате рождения'
        ];
        foreach ($sorts as $key => $label) {
            $active = ($sort == $key) ? 'select' : '';
            echo "<a href='?action=view&sort=$key&page=1' class='$active'>$label</a>";
        }
        ?>
    </div>
    <?php endif; ?>
</aside>