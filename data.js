// Datos de Talleres Abiertos - FAB LAB Perú (edu.fab.pe)
// Sincronizado automáticamente desde el Panel Admin de FAB LAB Perú
// Última actualización: 2026-09-08 21:34:33

const SITE_DATA = {
  phone: "+51 989 984 480",
  whatsappBase: "51989984480",
  brandName: "FAB LAB Perú",
  brandTagline: "Innovación y Fabricación Digital",
  mainSiteUrl: "https://fab.pe"
};

const CATEGORIES = [
  { id: "all", name: "Todos los Talleres" },
  { id: "kids", name: "Niños y Adolescentes" },
  { id: "creativos", name: "Jóvenes & Creativos" },
  { id: "profesionales", name: "Adultos & Profesionales" }
];

var WORKSHOPS = [
    {
        "id": "minicuadros-mural-2-5d",
        "title": "Minicuadros & Composición Mural 2.5D",
        "subtitle": "Transforma bocetos en minicuadros tridimensionales por capas en MDF con corte láser y acabados en acrílico.",
        "category": "creativos",
        "targetAudience": "Jóvenes, Adultos, Artistas, Diseñadores y Entusiastas Maker",
        "badge": "Arte & Corte Láser",
        "price": "S\/. 200",
        "startDate": "Martes 7 de Octubre",
        "duration": "4 sesiones (8 hrs)",
        "schedule": "Martes de 7:00 pm a 9:00 pm",
        "format": "Híbrido (Teoría\/Diseño virtual + Corte y Exposición presencial en Fab Lab Miraflores)",
        "fabTool": "Inkscape \/ Cuttle & Cortadora Láser CO2",
        "challenge": "Diseñar virtualmente un cuadro en relieve 2.5D en capas de MDF, fabricarlo en corte láser, aplicar acabados acrílicos y exponerlo en la Feria FABulosa.",
        "description": "Un laboratorio práctico de 4 sesiones donde transformarás tus ilustraciones y bocetos a mano en un minicuadro tridimensional por capas en MDF. Exploraremos el diseño vectorial, la profundización por niveles (2.5D), el corte y grabado láser, y técnicas de acabado con pintura acrílica para culminar con la creación de una obra personal y una composición colectiva de gran formato en la Feria FABulosa (Exposición final el 31 de Octubre en Hub Miraflores).",
        "syllabus": [
            {
                "session": "Misión 1",
                "title": "Descubrir e Ilustrar (Virtual)",
                "desc": "Explora la lógica del arte en capas 2.5D. Concebirás una ilustración temática desglosada en un boceto estructurado por niveles (mínimo 3 capas)."
            },
            {
                "session": "Misión 2",
                "title": "Vectorizar y CAM (Virtual)",
                "desc": "Software vectorial (Inkscape\/Cuttle) para convertir tu boceto en trazados digitales limpios (corte vs. grabado), tolerancias y parámetros de máquina."
            },
            {
                "session": "Misión 3",
                "title": "Fabricar y Pintar (Presencial)",
                "desc": "Corte láser presencial en Fab Lab Miraflores en planchas de MDF 3mm, aplicación de pintura acrílica y ensamble estructural con adhesivo de madera."
            },
            {
                "session": "Misión 4",
                "title": "Exposición y Montaje Colectivo (Presencial)",
                "desc": "Montaje en galería temática en Fab Lab Miraflores, diálogo del proceso creativo y exhibición pública final en la Feria FABulosa (31 de Octubre)."
            }
        ],
        "highlights": [
            "Diseño y vectorización 2.5D desglosando ilustraciones en estructuras multicapa.",
            "Flujo CAD\/CAM completo y corte directo en la cortadora láser CO2 de Fab Lab Miraflores.",
            "Acabados artísticos con pintura acrílica, contrastes de color y técnicas de ensamble.",
            "Presentación de tu obra personal y colectiva en la exposición de la Feria FABulosa."
        ],
        "image": "images\/taller-minicuadros-25d.jpg",
        "instructor": "Evelyn Andrea Cuadrado Guerrero",
        "instructorEmail": "contacto@fablablima.org",
        "status": "published",
        "createdAt": "2026-09-08T16:25:00.000Z"
    },
    {
        "id": "open-circuits-mblock",
        "title": "OPEN CIRCUITS: Robótica, IA y Videojuegos con mBlock",
        "subtitle": "Curso práctico de tecnología creativa que integra circuitos, sensores, visión artificial y diseño de videojuegos.",
        "category": "kids",
        "targetAudience": "Niños y adolescentes de 10 a 15 años",
        "badge": "Robótica & IA Kids",
        "price": "S\/. 400",
        "startDate": "Sábado 25 de Octubre",
        "duration": "8 sesiones (16 hrs)",
        "schedule": "Sábados de 3:30 pm a 5:30 pm",
        "format": "Híbrido (Clases virtuales interactivas y retos prácticos de laboratorio)",
        "fabTool": "mBlock, mlink2, Sensores, Actuadores y Cámara ML",
        "challenge": "Programar sensores y actuadores, entrenar un modelo de Machine Learning para reconocimiento de gestos y crear un videojuego funcional integrador.",
        "description": "Curso de tecnología creativa que integra robótica, inteligencia artificial y diseño de videojuegos mediante mBlock. Los estudiantes programan sensores y actuadores, entrenan modelos simples de reconocimiento y desarrollan videojuegos. Fomenta la creatividad y el aprendizaje basado en proyectos mediante un proyecto final integrador.",
        "syllabus": [
            {
                "session": "Misión 1",
                "title": "Descubrir el Entorno mBlock",
                "desc": "Explora mBlock y reconoce los fundamentos de robótica, sensores y actuadores mediante ejemplos prácticos."
            },
            {
                "session": "Misión 2",
                "title": "Digitalizar & Programar Salidas",
                "desc": "Programa bloques para controlar LEDs, semáforos o actuadores experimentando con entradas y salidas digitales."
            },
            {
                "session": "Misión 3",
                "title": "Modelar Circuitos Interactivos",
                "desc": "Diseña y construye un circuito o robot interactivo que detecte una condición ambiental y genere una respuesta."
            },
            {
                "session": "Misión 4",
                "title": "Elegir Tecnología de IA",
                "desc": "Explora la inteligencia artificial y compara aplicaciones de reconocimiento de gestos para tu proyecto."
            },
            {
                "session": "Misión 5",
                "title": "Preparar y Entrenar Modelos ML",
                "desc": "Captura muestras y entrena un modelo de machine learning en vivo, realizando pruebas y ajustes de precisión."
            },
            {
                "session": "Misión 6",
                "title": "Diseñar y Programar Videojuegos",
                "desc": "Diseña un videojuego en mBlock incorporando personajes, escenarios, movimiento, reglas, puntaje y colisiones."
            },
            {
                "session": "Misión 7",
                "title": "Aplicar y Compartir Proyecto Final",
                "desc": "Desarrolla un proyecto integrador de libre elección aplicando IA, robótica o videojuegos y preséntalo al grupo."
            }
        ],
        "highlights": [
            "Programación visual y control de componentes electrónicos, sensores y motores.",
            "Entrenamiento de modelos de visión artificial e inteligencia artificial sin código engorroso.",
            "Desarrollo completo de videojuegos interactivos desde el diseño visual hasta las mecánicas.",
            "Proyecto final integrador de libre elección guiado paso a paso por instructores especialistas."
        ],
        "image": "images\/taller-open-circuits.jpg",
        "instructor": "Hayashi Mateo y Francheska Baca",
        "instructorEmail": "contacto@fablablima.org",
        "status": "published",
        "createdAt": "2026-09-08T16:25:00.000Z"
    },
    {
        "id": "digitoys-fabricacion-digital",
        "title": "Digitoys: Fabricación Digital de Juguetes & Autómatas",
        "subtitle": "Diseña, corta con láser y ensambla tus propios juguetes mecánicos articulados con movimiento.",
        "category": "kids",
        "targetAudience": "Niños de 7 a 13 años y familias creativas",
        "badge": "Juguetes & Autómatas",
        "price": "S\/. 200",
        "startDate": "Sábado 25 de Octubre",
        "duration": "4 sesiones (6 hrs)",
        "schedule": "Sábados de 10:00 am a 11:30 am",
        "format": "Presencial en Fab Lab Miraflores",
        "fabTool": "Cortadora Láser CO2, Impresión 3D y Mecanismos de Ensamble",
        "challenge": "Idear, vectorizar y ensamblar un autómata mecánico de madera con sistema de manivela, levas y articulaciones móviles.",
        "description": "Digitoys es el emblemático programa maker donde niños y niñas dan vida a juguetes interactivos mediante fabricación digital. A través de retos manuales y digitales, los participantes descubren el fascinante mundo de la cinemática lúdica: diseñan personajes articulados en madera y acrílico, operan la cortadora láser e impresora 3D, y calibran engranajes y levas para construir su propio autómata mecánico funcional.",
        "syllabus": [
            {
                "session": "Misión 1",
                "title": "El Mundo de los Autómatas",
                "desc": "Exploración de la mecánica del movimiento: levas, bielas, manivelas y bocetado del personaje o criatura mecánica."
            },
            {
                "session": "Misión 2",
                "title": "Diseño Digital y Piezas Press-Fit",
                "desc": "Trazado digital de los eslabones y engranajes con tolerancias precisas de encaje a presión sin pegamento."
            },
            {
                "session": "Misión 3",
                "title": "Corte Láser y Fabricación en el Lab",
                "desc": "Operación segura de la máquina láser en MDF y acrílico, más piezas complementarias en impresión 3D."
            },
            {
                "session": "Misión 4",
                "title": "Calibración, Acabados y Show Digitoys",
                "desc": "Ensamble cinemático, lubricación de ejes, personalización artística y presentación interactiva de cada juguete."
            }
        ],
        "highlights": [
            "Aprende cinemática y física aplicada a través del juego y la construcción real.",
            "Uso práctico de máquinas de alta tecnología: cortadora láser CO2 e impresoras 3D.",
            "Desarrollo del pensamiento espacial, motricidad fina y resolución de retos mecánicos.",
            "Cada participante fabrica y se lleva a casa su autómata mecánico 100% funcional."
        ],
        "image": "images\/taller-digitoys.jpg",
        "instructor": "Henry Sánchez",
        "instructorEmail": "contacto@fablablima.org",
        "status": "published",
        "createdAt": "2026-09-08T16:25:00.000Z"
    }
];
if (typeof window !== 'undefined') window.WORKSHOPS = WORKSHOPS;
