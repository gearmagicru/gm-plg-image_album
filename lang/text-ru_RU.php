<?php
/**
 * Этот файл является частью виджета веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Фотоальбом',
    '{description}' => 'Фотоальбом медиагалереи',

    // GalleryEditWindow
    '{gallery.title}' => 'Добавление фотоальбома',
    '{gallery.titleTpl}' => 'Изменение фотоальбома "{name}"',
    // GalleryEditWindow: вкладки
    'Common' => 'Общие',
    'Images' => 'Изображения',
    'Add and continue' => 'Добавить и продолжить?',
    'To add items to a gallery on this tab, you must first add a gallery. Add and continue?' 
        => 'Для добавления элементов в альбом на вкладке "Загрузка", необходимо альбом сначала добавить. Добавить и продолжить?',
    // GalleryEditWindow: поля
    'Name' => 'Название',
    'Description' => 'Описание',
    'Visible' => 'Видимый',
    'Author' => 'Автор',
    'Path' => 'Путь',
    'Cover' => 'Обложка',
    // GalleryEditWindow: вкладка
    'Options' => 'Настройки',
    'Uploads' => 'Загрузка',
    'Here you specify the size of the images that will be uploaded to the gallery and whether a watermark will be added to the image' 
        => 'Здесь указывают, с какими размера будет загружаться изображения в альбом и будет ли на изображении добавлен водяной знак',
    // GalleryEditWindow: вкладка "общие" / поля
    'Image settings' => 'Настройка изображений',
    'Thumbnail size' => 'Размер миниатюры',
    'Max. width' => 'Максимальная ширина, пкс',
    'Maximum width in pixels' => 'Максимальная ширина в пикселях',
    'Max. height' => 'Максимальная высота, пкс',
    'Maximum height in pixels' => 'Максимальная высота в пикселях',
    'Add a watermark' => 'Добавить водяной знак',
    'Crop image to size' => 'Обрезать изображение по размеру',
    'Original size' => 'Оригинальный размер',
    'Published' => 'Опубликован',
    // GalleryEditWindow: сообщения / текст
    'Error saving image album files' => 'Ошибка сохранения файлов фотоальбома.',
    'Error adding image album' => 'Ошибка добавления фотоальбома.',
    'Error deleting image album' => 'Ошибка удаления фотоальбома',
    'Error deleting image album folder "{0}"' => 'Ошибка удаления папки фотоальбома "{0}".',

    // TabItemsGrid
    'Image album "{0}"' => 'Фотоальбом "{0}"',
    // TabItemsGrid: контекстное меню записи
    'Edit' => 'Редактировать',
    // TabItemsGrid: панель инструментов
    'Adding a new image' => 'Добавление изображения в фотоальбом',
    'Adding a new images' => 'Добавление изображений в фотоальбом',
    'Deleting selected images' => 'Удаление выделенных изображений из фотоальбома',
    'Deleting all images' => 'Удаление всех изображений из фотоальбома',
    // TabItemsGrid: столбцы
    'Sequence number in the list' => 'Порядковый номер в списке',
    'Image' => 'Изображение',
    'Format' => 'Формат',
    'Image format' => 'Формат файла изображения',
    'Image identifier' => 'Идентификатор изображения',
    'Filename' => 'Нзвание файла',
    'Size (Mb)' => 'Размер (Мб)',
    'File size (Mb)' => 'Размер файла (Мб)',
    'Size (px)' => 'Размер (пкс)',
    'Image size in pixels' => 'Размер изображения в пикселях',
    'Thumb' => 'Миниатюра',
    'Thumbnail image size in pixels' => 'Размер изображения миниатюры в пикселях',
    'The image is displayed in the image album' => 'Изображение отображается в фотоальбоме',
    'Visible' => 'Отображается',

    // ItemEditWindow
    '{item.title}' => 'Добавление изображения',
    '{item.titleTpl}' => 'Изменение изображения "{title}"',
    // ItemEditWindow: поля
    'The image album settings will be applied to the uploaded image file' 
        => 'К загруженному файлу изображения будут применены настройки фотоальбома',
    // ItemEditWindow: сообщения / текст
    'Error deleting album image' => 'Ошибка удаления изображения фотоальбома.',
    'Error deleting image file "{0}"' => 'Ошибка удаления файла изображения "{0}".',
    'Error adding album image' => 'Ошибка добавления изображения в фотоальбом',
    'Error creating image album folder "{0}"' => 'Ошибка создания папки фотоальбома "{0}"',

    // Settings
    '{settings.title}' => 'Настройки плагина "Фотоальбом"',
    'Template' => 'Шаблон',
    'Album image folder template' => 'Шаблон создания папки изображений фотоальбома',
    'Create a folder and move files there' => 'Создать папку и переместить туда файлы',
    'Symbol in template' => 'Символы в шаблоне:<br>',
    '{Year} - year number, 2 digits' => '<b>{Year}</b> - номер года, 2 цифры;<br>',
    '{year} - full numeric representation of the year, at least 4 digits' 
        => '<b>{year}</b> - полное числовое представление года, не менее 4 цифр;<br>',
    '{Month} - serial number of the month without a leading zero' 
        => '<b>{Month}</b> - порядковый номер месяца без ведущего нуля (например, <em>от 1 до 12</em> );<br>',
    '{month} - serial number of the month with a leading zero' 
        => '<b>{month}</b> - порядковый номер месяца с ведущим нулём (например, <em>от 01 до 12</em> );<br>',
    '{Day} - day of the month without a leading zero' 
        => '<b>{Day}</b> - день месяца без ведущего нуля (например, <em>от 1 до 31</em> );<br>',
    '{day} - day of the month, 2 digits with leading zero' 
        => '<b>{day}</b> - день месяца, 2 цифры с ведущим нулём (например, <em>от 01 до 31</em> );<br>',
    '{id} - image album identifier' 
        => '<b>{id}</b> - идентификатор фотоальбома.',
    'Watermark' => 'Водяной знак',
    'Filename' => 'Файл',
    'Filename watermak' => 'Файл изображения водяного знака',
    'Opacity' => 'Прозрачность',
    'Position' => 'Положение',
    'Offset X' => 'Смещение по горизонтали, пкс.',
    'Offset Y' => 'Смещение по вертикали, пкс.',
    'Right bottom' => 'Справа снизу',
    'Right center' => 'Справа по центру',
    'Right top' => 'Справа сверху',
    'Left center' => 'Слева по центру',
    'Left top' => 'Слева сверху',
    'Left bottom' => 'Слева снизу',
    'Bottom center' => 'Снизу по центру',
    'Center' => 'По центру',
    'Top center' => 'Сверху по центру'
];
