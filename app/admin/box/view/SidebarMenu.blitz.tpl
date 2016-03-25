<ul id="main-menu" class="main-menu">
    <!-- add class "multiple-expanded" to allow multiple submenus to open -->
    <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
    <li>
        <a href="#">
            <i class="entypo-gauge"></i>
            <span class="title">Pulpit</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="entypo-trophy"></i>
            <span class="title">Artefakty</span>
        </a>
        <ul>
            <li>
                <a href="{{this::url("Artifact::manage")}}">
                    <span class="title">Zarządzaj</span>
                </a>
            </li>
            <li>
                <a href="{{this::url("Artifact::onHomepage")}}">
                    <span class="title">Na stronie głównej</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{this::url('Adverts')}}">
            <i class="entypo-sound"></i>
            <span class="title">Reklama</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="entypo-users"></i>
            <span class="title">Użytkownicy</span>
        </a>
            <ul>
                <li>
                    <a href="{{this::url("User::overview")}}" class="admins-list">
                        <span class="title">Lista użytkowników</span>
                    </a>
                </li>
                <li>
                    <a href="{{this::url("User::disallowUpload")}}" class="admins-add">
                        <span class="title">Blokada uploadu</span>
                    </a>
                </li>
            </ul>
    </li>
    <li>
        <a href="#">
            <i class="entypo-tools"></i>
            <span class="title">Administracja</span>
        </a>
        <ul>
            <li>
                <a href="{{this::url("Admin::overview")}}" class="admins-list">
                    <span class="title">Lista administratorów</span>
                </a>
            </li>
            <li>
                <a href="{{this::url("Admin::add")}}" class="admins-add">
                    <span class="title">Dodaj administratora</span>
                </a>
            </li>
        </ul>
    </li>
</ul>
