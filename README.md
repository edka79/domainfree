## Сервис по поиску освобождающихся доменов

## Алгоритм работы

<li>Скачиваем каждые сутки новый csv файл от регистратора доменов и сохраняем в БД</li>
<li>Скачиваем каждые сутки новый csv файл от сервиса expired.ru - (там есть дополнительные данные по доменам) и сохраняем в БД</li>
<li>Каждые сутки обновляем главную агрегирующую таблицу agregations куда попадают только домены, срок регистрации которых истекает через 30 дней (~ 180 000 доменов), в эту таблицу записываются данные из источников п.1 и п. 2</li>
<li>Данные из таблицы agregations выводятся на фронтенд при помощи Vue.js данные можно фильтровать и сортировать по всем доступным полям таблицы agregations</li>
<li>Также есть еще таблицы-словари русский и английских слов: dir_words_english и dir_words_russian. Данные из этих таблиц используются для сравнения домена со словарным словом, чтобы сделать пометку в базе, что домен является ключевым словом. Это помогает найти домены, которые являются целиком словарным словом (имеет ценность для сео специалистов).</li>
<li>Еще есть раздел “Домены ключевики, свободные” - который показывает все свободные на данный момент домены, которые совпали с данными из таблиц-словарей: dir_words_english и dir_words_russian и при этом отсутвуют среди зарегистрированных.</li>
<li>И есть раздел “Домены в закладках” - где можно посмотреть все домены, добавленные в избранное.</li>

