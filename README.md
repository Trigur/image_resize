# image_resize
Изменение размеров изображения на лету.

Модуль использует библиотеку imagick.

<h4>Алгоритмы взяты отсюда:</h4>
<p><a href="https://github.com/tim-reynolds/crop/tree/UpdateEntropyAlgorithm">https://github.com/tim-reynolds/crop/tree/UpdateEntropyAlgorithm</a></p>

<h4>Использование:</h4>
<p><b>В админке</b>: "Модули" -> "Все модули" -> "Установить модули" -> "Установка" напротив "Image Resize".</p>
<p><b>В шаблоне</b>: &lt;img src="{magickCrop($page['field_image'], 300, 300)}"&gt;</p>

<h4>Доступные функции:</h4>
<p>Изменнение размера изображения с обрезанием.<p>
<p>magickCrop(<br>
&nbsp;&nbsp;&nbsp;&nbsp;путь к изображению. Указывайте относительно (например: /uploads/images/logo.jpg).,<br>
&nbsp;&nbsp;&nbsp;&nbsp;ширина || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;высота || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;качество изображения: от 1 до 100 (рекомендуемое: 70),<br>
&nbsp;&nbsp;&nbsp;&nbsp;тип: [center, entropy, balanced, face],<br>
&nbsp;&nbsp;&nbsp;&nbsp;заменять ли исходное изображение<br>
);</p>

<p>Изменнение размера изображения без обрезания.</p>
<p>magickScale(<br>
&nbsp;&nbsp;&nbsp;&nbsp;путь к изображению. Указывайте относительно (например: /uploads/images/logo.jpg).,<br>
&nbsp;&nbsp;&nbsp;&nbsp;ширина || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;высота || null,<br>
&nbsp;&nbsp;&nbsp;&nbsp;качество изображения: от 1 до 100 (рекомендуемое: 70),<br>
&nbsp;&nbsp;&nbsp;&nbsp;заменять ли исходное изображение<br>
);</p>
