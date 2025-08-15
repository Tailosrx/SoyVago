<?php
session_start();
require_once 'config/db.php';
require_once 'controllers/get_ajustes.php';





?>

<!DOCTYPE html>
<html>

<head>
    <title>Ajustes - SoyVago</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        .ajustes-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .ajuste-seccion {
            background: #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #3a3a3a;
        }

        .ajuste-seccion h3 {
            color: #db9e1b;
            margin-top: 0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ajuste-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #3a3a3a;
        }

        .ajuste-item:last-child {
            border-bottom: none;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #3a3a3a;
            transition: .3s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #db9e1b;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(26px);
        }

        .color-options {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: white;
            box-shadow: 0 0 0 2px #db9e1b;
        }

        .btn-guardar {
            background: linear-gradient(135deg, #db9e1b, #c47d7f);
            color: #131212;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .texto-ayuda {
            font-size: 0.9rem;
            color: #b0b0b0;
            margin-top: 5px;
        }
    </style>
</head>

<body class="<?= $ajustes['modo_oscuro'] ? 'modo-oscuro' : 'modo-diurno' ?>">
    <div class="app-container">
        <aside class="vertical-sidebar">
            <nav>
                <section class="sidebar__wrapper">
                    <ul class="sidebar__list list--primary">
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="dashboard.php" data-tooltip="Dashboard">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-house-door"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
                                    </svg>
                                </span>
                                <span class="text">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="mis_metas.php" data-tooltip="Mis Metas">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-bullseye"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="M8 13A5 5 0 1 1 8 3a5 5 0 0 1 0 10zm0 1A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
                                        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                                        <path d="M9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                    </svg>
                                </span>
                                <span class="text">Mis Metas</span>
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="#" data-tooltip="Tareas/Hábitos">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-check-circle"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                                    </svg>
                                </span>
                                <span class="text">Tareas/Hábitos</span>
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__link" href="#" data-tooltip="Calendario">
                                <span class="icon">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-calendar"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                                    </svg>
                                </span>
                                <span class="text">Calendario</span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <ul class="sidebar__list list--secondary">
                        <li class="sidebar__item"> <a class="sidebar__link" href="#" data-tooltip="Profile"> <span
                                    class="icon"> <svg width="16" height="16" fill="currentColor"
                                        class="bi bi-person-circle" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                        <path fill-rule="evenodd"
                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                    </svg> </span> <span class="text">Mi Cuenta</span> </a> </li>
                        <li class="sidebar__item"> <a class="sidebar__link" href="ajustes.php" data-tooltip="Settings">
                                <span class="icon"> <svg width="16" height="16" fill="currentColor" class="bi bi-gear"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                        <path
                                            d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                                    </svg> </span> <span class="text">Ajustes</span> </a> </li>
                        <li class="sidebar__item"> <a class="sidebar__link" href="logout.php" data-tooltip="Logout">
                                <span class="icon"> <svg width="16" height="16" fill="currentColor"
                                        class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                        <path fill-rule="evenodd"
                                            d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                                    </svg> </span> <span class="text">Salir</span> </a> </li>
                    </ul>
                </section>
            </nav>
        </aside>

        <main class="main-content">
            <h2 class="center-title">Ajustes</h2>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="mensaje-exito">
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <div class="ajustes-container">
                <form method="POST">
                    <!-- Sección Notificaciones -->
                    <div class="ajuste-seccion">
                        <h3> Notificaciones</h3>

                        <div class="ajuste-item">
                            <div>
                                <h4>Recordatorios</h4>
                                <p class="texto-ayuda">Recibe avisos amables de tus metas</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notificaciones" <?= $ajustes['notificaciones'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="ajuste-item">
                            <div>
                                <h4>Modo "Tranqui"</h4>
                                <p class="texto-ayuda">Notificaciones más relajadas los fines</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="modo_tranqui" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Sección Apariencia -->
                    <div class="ajuste-seccion">
                        <h3> Apariencia</h3>

                        <div class="ajuste-item">
                            <div>
                                <h4>Modo oscuro</h4>
                                <p class="texto-ayuda">Para buhos nocturnos</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="modo_oscuro" <?= $ajustes['modo_oscuro'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="ajuste-item" style="flex-direction: column; align-items: flex-start;">
                            <h4>Color de los Iconos</h4>
                            <div class="color-options">
                                <div class="color-option selected" style="background: #db9e1b;" data-color="#db9e1b"></div>
                                <div class="color-option" style="background: #c47d7f;" data-color="#c47d7f"></div>
                                <div class="color-option" style="background: #4C6B9F;" data-color="#4C6B9F"></div>
                                <div class="color-option" style="background: #2ed573;" data-color="#2ed573"></div>
                            </div>
                            <input type="hidden" name="color_principal" id="color-principal"
                                value="<?= $ajustes['color_principal'] ?>">
                        </div>
                    </div>

                    <!-- Sección Cuenta -->
                    <div class="ajuste-seccion">
                        <h3> Cuenta</h3>

                        <div class="ajuste-item">
                            <div>
                                <h4>Exportar datos</h4>
                                <p class="texto-ayuda">Descarga tu información en JSON</p>
                            </div>
                            <a href="exportar.php" class="btn-exportar">Exportar</a>
                        </div>

                        <div class="ajuste-item">
                            <div>
                                <h4>Eliminar cuenta</h4>
                                <p class="texto-ayuda">Adiós para siempre (no lo hagas)</p>
                            </div>
                            <button type="button" class="btn-eliminar" id="btn-eliminar">Eliminar</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const toggle = document.querySelector('input[name="modo_oscuro"]');

            toggle.addEventListener('change', function () {
                const body = document.body;
                if (this.checked) {
                    body.classList.remove('modo-diurno');
                    body.classList.add('modo-oscuro');
                } else {
                    body.classList.remove('modo-oscuro');
                    body.classList.add('modo-diurno');
                }
            });

            const colorOptions = document.querySelectorAll('.color-option');
            const colorPrincipalInput = document.getElementById('color-principal');

            // Función para aplicar el color
            function applyIconColor(color) {
                document.querySelectorAll('.sidebar svg').forEach(svg => {
                    svg.style.setProperty('fill', color, 'important'); // Usamos important aquí también
                });
            }

            colorOptions.forEach(option => {
                option.addEventListener('click', function () {
                    colorOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');

                    const selectedColor = this.getAttribute('data-color');
                    colorPrincipalInput.value = selectedColor;

                    // Aplicar el color
                    applyIconColor(selectedColor);
                });
            });

            const savedColor = "<?= $ajustes['color_principal'] ?>";
            if (savedColor) {
                applyIconColor(savedColor);

                const savedOption = document.querySelector(`.color-option[data-color="${savedColor}"]`);
                if (savedOption) {
                    savedOption.classList.add('selected');
                } else {
                    colorOptions[0]?.classList.add('selected');
                }
            } else {
                colorOptions[0]?.classList.add('selected');
            }


        });

    </script>
</body>

</html>