@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="hp-main-header">Менеджер учебного расписания, это:</h1>
        <ul>
            <li>
                <h2 class="hp-header">Работа со списками преподавателей, студенческих групп и учебных занятий</h2>
                <p class="hp-text"><span class="hp-capital-letter">С</span>писки содержат основные характеристики преподавателей, студенческих групп и учебных пар. Есть возможность их
                фильтрации одновременно по множеству характеристик, сортировки и пагинации.<br><span class="hp-capital-letter">Д</span>ля открытия списка, например,
                преподавателей необходимо перейти по ссылке "Преподаватели -> Список" основного меню. Аналогично - открытие списка
                студенческих групп и учебных занятий. </p>
            </li>
            <li>
                <h2 class="hp-header">Расписание занятий преподавателей и студенческих групп</h2>
                <p class="hp-text"><span class="hp-capital-letter">Р</span>асписание представлено в виде матрицы (дни недели * учебные пары). Если занятие происходит по красной неделе,
                его информация размещена в верхней части ячейки матрицы красного цвета; если по синей - в нижней синего цвета.
                По каждому занятию представлены сведения о предмете, виде занятия (лекция, практическое или лабораторное занятие),
                аудитории, а также названии группы (если это расписание преподавателя) или должности и имени преподавателя, если
                это расписание группы. В одном занятии может принимать участие несколько групп. Тогда в названии группы они все будут
                обозначены в квадратных скобках через дифис.<br><span class="hp-capital-letter">П</span>о умолчанию выводится регулярное расписание. Но можно выбрать неделю
                и получить расписание для неё с указанием одноразовых занятий, назначенных на определённую дату. В ячеке такого
                занятия кроме прочего указана и его дата.<br><span class="hp-capital-letter">Д</span>ля перехода к расписанию преподавателя необходимо нажать на его имя
                в списке преподавателей. Аналогично - для перехода к расписанию группы.</p>
            </li>
            <li>
                <h2 class="hp-header">Варианты замены занятий</h2>
                <p class="hp-text"><span class="hp-capital-letter">З</span>амена занятия - это когда первый преподаватель проводит своё занятие вместо второго в его расписание, а потом
                второй проводит своё занятие в этой же группе но по расписанию первого. Замена проводится, если преподаватель по той
                или иной причине не может провести занятие в своё расписание.<br><span class="hp-capital-letter">В</span> данном случае необходимо среди
                всех преподавателей группы заменяемого занятия найти тех, у кого нет своего занятия во время заменяемого. Потом среди них
                выбрать тех, у кого есть занятия в указанной группе в то время, когда заменяемый преподаватель свободен. Такие занятия
                и являются вариантами замены.<br><span class="hp-capital-letter">В</span>арианты замены представлены в виде списка занятий с характеристиками и матрицы расписания
                заменяемого преподавателя, в которую добавлены варианты замены. Ячейка заменяемого занятия окрашена в жёлтый, а варианта
                замены - в зелёный.<br><span class="hp-capital-letter">П</span>о умолчаню. при подборе вариантов замены учитывается только регулярное расписание. Но если указать
                неделю, то можно получить результат конкретно для неё.<br><span class="hp-capital-letter">Е</span>сть возможность фильтрации вариантов одновременно по нескольким
                характеристикам<br><span class="hp-capital-letter">Ч</span>тобы получить варианты замены занятия, необходимо нажать на заменяемое занятие в расписании, и в
                открывшемся меню выбрать "Варианты замены"</p>
            </li>
            <li>
                <h2 class="hp-header">Варианты переноса занятий</h2>
                <p class="hp-text"><span class="hp-capital-letter">П</span>еренос занятия - это когда оно проводится в другое время в расписании, которое свободно и у преподавателя и у
                группы. Такие свободные места в расписании и являются варантами переноса занятия.<br><span class="hp-capital-letter">В</span>арианты переноса представлены в виде
                матрицы расписания, в которой цветом отмечены ячейки, соответствующие свободным для переноса местам. Цвет ячеек указывает
                на недельную периодичность.<br><span class="hp-capital-letter">П</span>о умолчаню, при подборе вариантов переноса учитывается только регулярное расписание. Но если
                указать неделю, то можно получить результат конкретно для неё.<br><span class="hp-capital-letter">Ч</span>тобы получить варианты переноса занятия, необходимо
                нажать на переносимое занятие в расписании, и в открывшемся меню выбрать "Варианты переноса"</p>
            </li>
            <li>
                <h2 class="hp-header">Варианты переноса занятий в расписании преподавателя и группы</h2>
                <p class="hp-text"><span class="hp-capital-letter">М</span>ожно рассматреть варианты переноса в расписании преподавателя или группы. В этом случае ячейка переносимого занятия
                окрашена в жёлтый, а вариантов переноса - в зелёный.<br><span class="hp-capital-letter">П</span>о умолчаню, при подборе вариантов переноса учитывается только
                регулярное расписание. Но если указать неделю, то можно получить результат конкретно для неё.<br><span class="hp-capital-letter">Ч</span>тобы получить варианты
                переноса занятия в расписании преподавателя или группы, необходимо нажать на переносимое занятие в расписании, в
                открывшемся меню выбрать "Варианты переноса" и затем под расписанием в разделе "Смотреть в расписании:" нажать на кнопку
                с названием преподавателя или группы.</p>
            </li>
            <li>
                <h2 class="hp-header">Экспорт в MS Word (формат docx)</h2>
                <p class="hp-text"><span class="hp-capital-letter">Д</span>ля того, чтобы внести свои поправки, распечатать и иметь при себе полученные результаты, предусмотрен их экспорт в MS Word (формат docx). Для
                этого необходимо нажать на кнопку "В MS Word" над любым из расписаний. Также экспортировать в MS Word можно список вариантов
                самены занятия.</p>
            </li>
        </ul>
        <div style="height: 100px;"></div>
    </div>

    <footer class="text-center text-lg-start fixed-bottom bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <p class="copyright-text text-center">Copyright &copy; 2022 Viktor Alikin. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

@endsection
