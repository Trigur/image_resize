# image_resize
Изменение размеров изображения на лету. Тестовая версия.

<h4>Модуль использует библиотеку imagick.</h4>
<p><a href="https://php.ru/manual/book.imagick.html" target="_blanck">Описание php.ru</a>.</p> 
<p><a href="http://www.php.net/class.imagick" target="_blanck">Описание php.net</a>.</p>
<p><a href="http://firstwiki.ru/index.php/%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0_Imagemagick">Установка</a>.</p>

<h4>Алгоритмы взяты отсюда:</h4>
<p><a href="https://github.com/tim-reynolds/crop/tree/UpdateEntropyAlgorithm">https://github.com/tim-reynolds/crop/tree/UpdateEntropyAlgorithm</a></p>

<h4>Использование:</h4>
<p><b>В админке</b>: "Модули" -> "Все модули" -> "Установить модули" -> "Установка" напротив "Image Resize".</p>
<p><b>В шаблоне</b>: <pre>&lt;img src="{magickCrop($page['field_image'], 300, 300)}"&gt;</pre></p>

<h4>Доступные функции:</h4>
<h5>Кадрирование (обрезка)</h5>
<pre>
<p>magickCrop(<br>
&nbsp;&nbsp;&nbsp;&nbsp;путь к изображению. Указывайте относительно (например: /uploads/images/logo.jpg).,<br>
&nbsp;&nbsp;&nbsp;&nbsp;ширина || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;высота || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;качество изображения: от 1 до 100 (рекомендуемое: 70),<br>
&nbsp;&nbsp;&nbsp;&nbsp;тип: [center, entropy, balanced, face],<br>
&nbsp;&nbsp;&nbsp;&nbsp;заменять ли исходное изображение<br>
);</p>
</pre>

<h5>Масштабирование</h5>
<pre>
<p>magickScale(<br>
&nbsp;&nbsp;&nbsp;&nbsp;путь к изображению. Указывайте относительно (например: /uploads/images/logo.jpg).,<br>
&nbsp;&nbsp;&nbsp;&nbsp;ширина || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;высота || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;качество изображения: от 1 до 100 (рекомендуемое: 70),<br>
&nbsp;&nbsp;&nbsp;&nbsp;заменять ли исходное изображение<br>
);</p>
</pre>
