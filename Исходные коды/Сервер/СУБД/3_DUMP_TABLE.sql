-- Дамп данных таблицы `subject`
--

INSERT INTO `subject` (`id_subject`, `subject_name`, `subject_note`, `subject_create_date`) VALUES
(0000000001, 'Операционные системы', 'III-семестр', '2015-03-27');

-- --------------------------------------------------------

--
-- Дамп данных таблицы `system`
--

INSERT INTO `system` (`id_system`, `system_name`) VALUES
(0000000002, 'Linux'),
(0000000001, 'Windows');

-- --------------------------------------------------------

--
-- Дамп данных таблицы `tgroup`
--

#INSERT INTO `tgroup` (`id_tgroup`, `tgroup_name`) VALUES
#(0000000001, 'КИ10-06'),
#(0000000002, 'КИ10-10');

-- --------------------------------------------------------

--
-- Дамп данных таблицы `user`
--

#INSERT INTO `user` (`id_user`, `user_login`, `user_sname`, `user_fname`, `user_pname`, `user_password`, `id_tgroup`) VALUES
#(0000000002, 'drach', 'Драч', 'Виктор', 'Андреевич', 'drach', 0000000002),
#(0000000003, 'test', 'Тест', 'Тест', 'Тестович', 'test', 0000000002),
#(0000000004, 'homutov', 'Хомутов', 'Михаил', 'Владимирович', 'homutov', 0000000002),
#(0000000005, 'alfimov', 'Алфимов', 'Дмитрий', 'Евгеньевич', 'alfimov', 0000000002),
#(0000000006, 'danilov', 'Данилов', 'Илья', 'Олегович', 'danilov', 0000000001),
#(0000000007, 'atonov', 'Антонов', 'Олег', 'Игоревич', 'antonov', 0000000001),
#(0000000008, 'ermolin', 'Ермолин', 'Павел', 'Андреевич', 'ermolin', 0000000001);

-- --------------------------------------------------------

--
-- Дамп данных таблицы `labwork`
--

INSERT INTO `labwork` (`id_labwork`, `labwork_name`, `labwork_bdate`, `labwork_edate`, `labwork_tlimit`, `labwork_climit`, `labwork_note`, `id_system`, `labwork_pcount`, `labwork_swel`, `labwork_available`, `id_subject`, `labwork_key`, `labwork_rift`) VALUES
(0000000001, 'Изучение работы команд Windows для работы с файлами', '2015-04-01', '2015-06-30', 120, 5, '-', 0000000001, 7, 'C:\\>', 1, 0000000001, '552c8c7950444', 70),
(0000000006, 'Изучение работы команд Linux для работы с файлами', '2015-05-14', '2016-05-08', 120, 5, '-', 0000000002, 17, '[student@vf ~]$ ', 1, 0000000001, '554b55a926d72',70);
--
-- Дамп данных таблицы `subject_has_tgroup`
--

#INSERT INTO `subject_has_tgroup` (`id_subject_has_tgroup`, `id_tgroup`, `id_subject`) VALUES
#(0000000001, 0000000001, 0000000001);

--
-- Дамп данных таблицы `problem`
--

INSERT INTO `problem` (`id_problem`, `problem_task`, `problem_tres`, `problem_fres`, `problem_command`, `problem_rwel`, `problem_climit`, `problem_foul`, `problem_note`, `id_labwork`, `problem_ind`, `problem_mscore`) VALUES
(0000000038, 'Вывести имя текущего каталога', 'C:\\;', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Команда введена неверно);', '(cd);', 'C:\\>', 3, 1, 'cd', 0000000001, 1, 3),
(0000000039, 'Перейти в папку C:\\USERS', '', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Системе не удается найти указанный путь.) (Команда введена неверно);', '(cd) (C:\\Users, Users);', 'C:\\>', 3, 1, '(cd) (C:\\Users, Users);', 0000000001, 2, 3),
(0000000040, 'Вывести список файлов и каталогов C:\\USERS', ' Том в устройстве C имеет метку OS\n Серийный номер тома: C2B8-A798\n\n Содержимое папки C:\\Users\n\n[.]                [..]               [Admin]            [All Users]\n[Default]          [Default User]     [Default.migrated] desktop.ini\n[Public]           [test]             [UpdatusUser]      [Все пользователи]\n               1 файлов            174 байт\n              11 папок  210 343 972 864 байт свободно;', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Команда введена неверно);\n("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Системе не удается найти указанный путь.) (Команда введена неверно);', '(dir) {/a, /w};\n(dir) (C:\\Users, C:\\Users\\) {/a, /w};', 'C:\\Users>', 3, 1, '(dir) {/a, /w};\n(dir) (C:\\Users, C:\\Users\\) {/a, /w};', 0000000001, 3, 3),
(0000000041, 'Создать папку TEST в C:\\USERS', '', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Системе не удается найти указанный путь.) (Команда введена неверно);', '(mkdir, md) (C:\\Users\\Test, Test);', 'C:\\Users>', 3, 1, '(mkdir, md) (C:\\Users\\Test, Test);', 0000000001, 4, 3),
(0000000042, 'Перейти в папку TEST', '', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Системе не удается найти указанный путь.) (Команда введена неверно);', '(cd) (C:\\Users\\Test\\, C:\\Users\\Test, Test);', 'C:\\Users\\Test>', 3, 1, '(cd) (C:\\Users\\Test\\, C:\\Users\\Test, Test);', 0000000001, 5, 3),
(0000000043, 'Скопировать файлы с расширением TXT из C:\\PROGRAM FILES\\FAR в C:\\USERS\\TEST', 'c:\\program files\\far\\FarCze.txt\nc:\\program files\\far\\FarEng.txt\nc:\\program files\\far\\FarGer.txt\nc:\\program files\\far\\FarHun.txt\nc:\\program files\\far\\FarIta.txt\nc:\\program files\\far\\FarPol.txt\nc:\\program files\\far\\FarRus.txt\nСкопировано файлов:         7.;', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Системе не удается найти указанный путь.) (Команда введена неверно);', '(copy, xcopy) ("c:\\program files\\far\\*.txt");', 'C:\\Users\\Test>', 3, 1, '(copy, xcopy) ("c:\\program files\\far\\*.txt");', 0000000001, 6, 3),
(0000000044, 'Отобразить атрибуты скопированных файлов', 'A            C:\\users\\test\\FarCze.txt\nA            C:\\users\\test\\FarEng.txt\nA            C:\\users\\test\\FarGer.txt\nA            C:\\users\\test\\FarHun.txt\nA            C:\\users\\test\\FarIta.txt\nA            C:\\users\\test\\FarPol.txt\nA            C:\\users\\test\\FarRus.txt;\nA            C:\\users\\test\\FarCze.txt\nA            C:\\users\\test\\FarEng.txt\nA            C:\\users\\test\\FarGer.txt\nA            C:\\users\\test\\FarHun.txt\nA            C:\\users\\test\\FarIta.txt\nA            C:\\users\\test\\FarPol.txt\nA            C:\\users\\test\\FarRus.txt;', '("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Команда введена неверно);\n("PAR" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.) (Не найден файл: PAR) (Команда введена неверно);', '(ATTRIB);\n(ATTRIB) (*.txt, c:\\users\\test\\*.txt);', 'C:\\Users\\Test>', 3, 1, '(ATTRIB);\n(ATTRIB) (*.txt, c:\\users\\test\\*.txt);', 0000000001, 7, 3),
(0000000076, 'Вывести имя текущего каталога', '/home/student;', '(-bash: PAR: command not found) (Команда задана неверно!);', '(pwd);', '[student@vf ~]$ ', 5, 1, '(pwd);', 0000000006, 1, 5),
(0000000077, 'Перейти в домашнюю папку', '', '(-bash: PAR: command not found) (Команда задана неверно!);\n(-bash: PAR: command not found) (-bash: cd: PAR: No such file or directory) (Команда задана неверно!);', '(cd);\n(cd) (~,$HOME,/home/student);', '[student@vf ~]$ ', 5, 1, '(cd);\n(cd) (~,$HOME,/home/student);', 0000000006, 2, 5),
(0000000078, 'Вывести список файлов и папок домашней директории', '.              .bash_profile  inf.php               .local\n..             .bashrc        inf.php.1             repomd.xml\n10_1_1.sh      .cache         install.sh            test\n10_1_1.sh.1    .config        kloxo-install         test1\n6QyHWnHG6      dfg            kloxo-installer.sh    .viminfo\nadf            ee             kloxo-installer.sh.1  vst-install-rhel.sh\n.bash_history  index.html     kloxo_install.log     vst-install.sh\n.bash_logout   index.html.1   kloxo-install.zip;', '(-bash: PAR: command not found) (Команда задана неверно!);\n(-bash: PAR: command not found) (-bash: ls: PAR: No such file or directory) (Команда задана неверно!);', '(ls) {-a}; (ls) (~,$HOME,/home/student) {-a};',  '[student@vf ~]$ ', 5, 1, '(ls) {-a}; (ls) (~,$HOME,/home/student) {-a};', 0000000006, 3, 5),
(0000000079, 'Создать папку test в домашней папке', '', '(-bash: PAR: command not found) (mkdir: cannot create directory ‘PAR’: No such file or directory\n) (Команда задана неверно!);', '(mkdir) (test,~/test,$HOME/test,/home/student/test);', '[student@vf ~]$ ', 5, 1, '(mkdir) (test,~/test,$HOME/test,/home/student/test);', 0000000006, 4, 5),
(0000000080, 'Перейти в папку test', '', '(-bash: PAR: command not found) (Команда задана неверно!);\n(-bash: PAR: command not found) (-bash: cd: PAR: No such file or directory) (Команда задана неверно!);', '(cd) (/home/student/test);', '[student@vf test]$ ', 5, 1, '(cd) (/home/student/test);', 0000000006, 5, 5),
(0000000081, 'Скопировать все файлы из /usr/share/automake в test', '', '(-bash: PAR: command not found) (cp: cannot stat ‘PAR’: No such file or directory\n) (cp: cannot stat ‘PAR’: No such file or directory\n) (Команда задана неверно!);', '(cp) (/usr/share/automake/*) (/home/student/test/);', '[student@vf test]$ ', 5, 1, '(cp) (/usr/share/automake/*) (/home/student/test/);', 0000000006, 6, 5),
(0000000082, 'Отобразить скопированные файлы с информацией о размере и правах доступа', 'total 584\n-rwxr-xr-x 1 student student   5826 May  7 17:51 ar-lib\n-rwxr-xr-x 1 student student   7333 May  7 17:51 compile\n-rwxr-xr-x 1 student student  45297 May  7 17:51 config.guess\n-rwxr-xr-x 1 student student  35533 May  7 17:51 config.sub\n-rw-r--r-- 1 student student  35147 May  7 17:51 COPYING.TXT\n-rwxr-xr-x 1 student student  23566 May  7 17:51 depcomp\n-rwxr-xr-x 1 student student  13997 May  7 17:51 install-sh\n-rw-r--r-- 1 student student  15749 May  7 17:51 INSTALL.TXT\n-rwxr-xr-x 1 student student   6047 May  7 17:51 mdate-sh\n-rwxr-xr-x 1 student student   6873 May  7 17:51 missing\n-rwxr-xr-x 1 student student   3538 May  7 17:51 mkinstalldirs\n-rwxr-xr-x 1 student student   4670 May  7 17:51 py-compile\n-rwxr-xr-x 1 student student  15285 May  7 17:51 tap-driver.pl\n-rwxr-xr-x 1 student student  19514 May  7 17:51 tap-driver.sh\n-rwxr-xr-x 1 student student   3977 May  7 17:51 test-driver\n-rw-r--r-- 1 student student 323102 May  7 17:51 texinfo.tex\n-rwxr-xr-x 1 student student  ', '(-bash: PAR: command not found) (Команда задана неверно!);', '(ls) {-l};', '[student@vf test]$ ', 5, 1, '(ls) {-l};', 0000000006, 7, 5),
(0000000083, 'Переименовать файл COMPILE в TEST', '', '(-bash: PAR: command not found) (mv: cannot stat ‘PAR’: No such file or directory) (mv: cannot stat ‘PAR’: No such file or directory) (Команда задана неверно!);', '(mv) (compile, ~/test/compile, $HOME/test/compile) (test, ~/test/test, $HOME/test/test);', '[student@vf test]$ ', 5, 1, '(mv) (compile, ~/test/compile, $HOME/test/compile) (test, ~/test/test, $HOME/test/test);', 0000000006, 8, 5),
(0000000084, 'Удалить файл TEST', '', '(-bash: PAR: command not found) (mv: cannot stat ‘PAR’: No such file or directory)  (Команда задана неверно!);', '(rm) (test, ~/test/test, $HOME/test/test);','[student@vf test]$ ', 5, 1, '(rm) (test, ~/test/test, $HOME/test/test);', 0000000006, 9, 5),
(0000000085, 'Найти номера строк в файле COPIYNG.TXT содержащие текст "provided"', '107:extent that warranties are provided), that licensees may convey the\n157:copyright on the Program, and are irrevocable provided the stated\n162:rights of fair use or other equivalent, as provided by copyright law.\n168:with facilities for running those works, provided that you comply with\n198:receive it, in any medium, provided that you conspicuously and\n212:terms of section 4, provided that you also meet all of these conditions:\n248:of sections 4 and 5, provided that you also convey the\n282:    that supports equivalent copying facilities, provided you maintain\n288:    e) Convey the object code using peer-to-peer transmission, provided\n337:  Corresponding Source conveyed, and Installation Information provided,\n395:of that license document, provided that the further restriction does\n410:provided under this License.  Any attempt otherwise to propagate or\n614:  If the disclaimer of warranty and limitation of liability provided;', '(-bash: PAR: command not found) (DEF) (grep: cannot stat ‘PAR’: No such file or directory\n) (Команда задана неверно!);', '(grep) (provided) (COPYING.TXT, ~/test/COPYING.TXT, $HOME/test/COPYING.TXT, /home/test/COPYING.TXT) {-n};', '[student@vf test]$ ', 5, 1, '(grep) (provided) (COPYING.TXT, ~/test/COPYING.TXT, $HOME/test/COPYING.TXT, /home/test/COPYING.TXT) {-n};', 0000000006, 10, 5),
(0000000086, 'Скопировать файлы с расширением txt в папку test из /usr/share/vim/vim74/doc с добавлением новых файлов', '', '(-bash: PAR: command not found) (cp: cannot stat ‘PAR’: No such file or directory\n) (cp: cannot stat ‘PAR’: No such file or directory\n) (Команда задана неверно!);', '(cp)  (/usr/share/vim/vim74/doc/*.txt) (~/test/, $HOME/test/, /home/student/test/);', '[student@vf test]$ ', 5, 1, '(cp)  (/usr/share/vim/vim74/doc/*.txt) (~/test/, $HOME/test/, /home/student/test/);', 0000000006, 11, 5),
(0000000087, 'Создать каталог proba в папке test', '', '(-bash: PAR: command not found) (mkdir: cannot create directory ‘PAR’: No such file or directory\n) (Команда задана неверно!);', '(mkdir) (~/test/proba,$HOME/test/proba,/home/student/test/proba);', '[student@vf test]$ ', 5, 1, '(mkdir) (~/test/proba,$HOME/test/proba,/home/student/test/proba);', 0000000006, 12, 5),
(0000000088, 'Переместить файлы из папки test в test/proba', '', '(-bash: PAR: command not found) (mv: cannot stat ‘PAR’: No such file or directory) (mv: cannot stat ‘PAR’: No such file or directory) (Команда задана неверно!);', '(mv) (~/test/*, $HOME/test/*, /home/student/test/*) (~/test/proba/, $HOME/test/proba/, /home/student/test/proba/);', '[student@vf test]$ ', 5, 1, '(mv) (~/test/*, $HOME/test/*, /home/student/test/*) (~/test/proba/, $HOME/test/proba/, /home/student/test/proba/);', 0000000006, 13, 5),
(0000000089, 'Удалить файлы из test/proba', '', '(-bash: PAR: command not found) (mv: cannot stat ‘PAR’: No such file or directory)  (Команда задана неверно!);', '(rm) (~/test/proba/*, $HOME/test/proba/*);', '[student@vf test]$ ', 5, 1, '(rm) (~/test/proba/*, $HOME/test/proba/*);', 0000000006, 14, 5),
(0000000090, 'Удалить каталог proba', '', '(-bash: PAR: command not found) (mv: cannot stat ‘PAR’: No such file or directory)  (Команда задана неверно!);', '(rmdir) (proba, ~/test/proba/, $HOME/test/proba/, ~/test/proba, $HOME/test/proba);', '[student@vf test]$ ', 5, 1, '(rmdir) (proba, ~/test/proba/, $HOME/test/proba/, ~/test/proba, $HOME/test/proba);', 0000000006, 15, 5),
(0000000091, 'Скопировать все файлы и папки из /usr/share/vim/vim74 в test', '', '(-bash: PAR: command not found) (cp: cannot stat ‘PAR’: No such file or directory\n) (cp: cannot stat ‘PAR’: No such file or directory\n) (Команда задана неверно!);', '(cp)  (/usr/share/vim/vim74/*) (~/test/, $HOME/test/, /home/student/test/);', '[student@vf test]$ ', 5, 1, '(cp)  (/usr/share/vim/vim74/*) (~/test/, $HOME/test/, /home/student/test/);', 0000000006, 16, 5),
(0000000092, 'Удалить папку test включая все ее файлы и подкаталоги', '', '(-bash: PAR: command not found) (mv: cannot stat ‘PAR’: No such file or directory)  (Команда задана неверно!);', '(rmdir) (~/test/, $HOME/test/, ~/test, $HOME/test);', '[student@vf test]$ ', 5, 1, '(rmdir) (~/test/, $HOME/test/, ~/test, $HOME/test);', 0000000006, 17, 5);


-- --------------------------------------------------------

--
-- Дамп данных таблицы `session`
--

-- --------------------------------------------------------

--
-- Дамп данных таблицы `statistic`
--

-- --------------------------------------------------------

