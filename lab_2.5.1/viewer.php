<?php
function getContactsTable($mysqli, $sort, $page) {
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    // Безопасная сортировка
    $allowedSorts = ['id', 'surname', 'birth_date'];
    $sort = in_array($sort, $allowedSorts) ? $sort : 'id';

    // Получаем записи
    $query = "SELECT * FROM contacts ORDER BY $sort ASC LIMIT $offset, $perPage";
    $result = mysqli_query($mysqli, $query);

    // Считаем общее количество
    $countResult = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM contacts");
    $total = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($total / $perPage);

    $html = '';

    if (mysqli_num_rows($result) == 0) {
        return '<p class="no-data">Записей пока нет. <a href="?action=add">Добавьте первую запись</a></p>';
    }

    $html .= '<table>';
    $html .= '<tr>
        <th>№</th>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Отчество</th>
        <th>Пол</th>
        <th>Дата рождения</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>Email</th>
        <th>Комментарий</th>
    </tr>';

    $num = $offset + 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . $num++ . '</td>';
        $html .= '<td>' . htmlspecialchars($row['surname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['lastname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['gender']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['birth_date']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['phone']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['address']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['email']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['comment']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    // Пагинация
    if ($totalPages > 1) {
        $html .= '<div class="pagination">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            $html .= "<a href='?action=view&sort=$sort&page=$i' class='$active'>$i</a> ";
        }
        $html .= '</div>';
    }

    $html .= '<p class="total">Всего записей: ' . $total . '</p>';

    mysqli_free_result($result);
    return $html;
}

echo getContactsTable($mysqli, $sort, $page);
?>