<?php

/**
 * Return list of users.
 */
function get_users($conn)
{
    $data = $conn->query(
        'select u.id, u.name
        from user_accounts as ua
        join transactions as t on ua.id = t.account_from or ua.id = t.account_to
        join users as u on ua.user_id = u.id');
    return $data ? $data->fetchAll(PDO::FETCH_KEY_PAIR) : [];
}

/**
 * Return transactions balances of given user.
 */
function get_user_transactions_balances($user_id, $conn)
{
    $sth = $conn->prepare(
        "SELECT
            u.name,
            STRFTIME('%Y-%m', t.trdate) AS month,
            COALESCE(SUM(CASE WHEN t.account_to IN (SELECT id FROM user_accounts WHERE user_id = u.id) THEN t.amount END), 0) -
            COALESCE(SUM(CASE WHEN t.account_from IN (SELECT id FROM user_accounts WHERE user_id = u.id) THEN t.amount END), 0) AS balance,
            COUNT(t.id) AS count
        FROM users u
            LEFT JOIN transactions t
                ON t.account_from IN (SELECT id FROM user_accounts WHERE user_id = u.id)
                    OR t.account_to IN (SELECT id FROM user_accounts WHERE user_id = u.id)
        WHERE u.ID = ?
        GROUP BY u.id, month;");
    $sth->bindParam(1, $user_id, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchAll();
}
