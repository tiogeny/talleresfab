// Datos de Talleres FAB LAB Perú (fab.pe)
// Edita textos, fechas, precios e instructores fácilmente

const SITE_DATA = {
  phone: "+51 964 359 899",
  whatsappBase: "51964359899",
  brandName: "FAB LAB Perú",
  brandTagline: "Democratizando la innovación tecnológica, social y patrimonial",
  mainSiteUrl: "https://fab.pe"
};

const CATEGORIES = [
  { id: "all", name: "Todos los Talleres" },
  { id: "kids", name: "Kids & Teens (6 - 15 años)" },
  { id: "patrimonio", name: "Neo-Artesanía & Cultura" },
  { id: "jovenes", name: "Jóvenes & Makers" },
  { id: "adultos", name: "Adultos & Profesionales" },
  { id: "instituciones", name: "Colegios & Instituciones" }
];

const WORKSHOPS = [
  {
    id: "digitoys",
    title: "Digitoys: Diseña y Fabrica tu Juguete 3D",
    subtitle: "Aprende modelado 3D interactivo y haz realidad tu juguete o personaje con impresión 3D",
    category: "kids",
    targetAudience: "Niños y niñas de 8 a 12 años",
    badge: "Más Popular",
    tagColor: "cyan",
    price: "S/. 150",
    startDate: "Sábado 18 de Octubre, 2026",
    duration: "4 sesiones (6 horas)",
    schedule: "Sábados de 10:00 am a 11:30 am",
    format: "Virtual en vivo + Fabricación física",
    fabTool: "Impresora 3D & Modelado Paramétrico",
    includes: [
      "4 sesiones interactivas con mentores FabLab",
      "10 FabCoins para fabricar su juguete en cualquier FabLab",
      "Archivos 3D editables y plantillas STL",
      "Certificado digital Maker Kids FAB LAB Perú"
    ],
    challenge: "Crear desde cero un personaje original en 3D, parametrizar tolerancias y enviarlo a fabricación física.",
    description: "Digitoys es el programa insignia donde los niños descubren el superpoder de la fabricación digital. Mediante dinámicas lúdicas y software intuitivo, diseñan su propio juguete, comprenden cómo funciona la física de los objetos y utilizan sus FabCoins para recibir su creación impresa en 3D.",
    syllabus: [
      { session: "Sesión 1", title: "El Universo Maker & Boceto", desc: "¿Qué es un FabLab? Conceptualización del personaje y diseño de proporciones." },
      { session: "Sesión 2", title: "Modelado 3D y Formas Primarias", desc: "Uso de herramientas de diseño 3D. Creación de cuerpo, brazos y articulaciones." },
      { session: "Sesión 3", title: "Texturas y Preparación para Impresión", desc: "Ajuste de tolerancias para encaje mecánico y aplicación de colores y relieves." },
      { session: "Sesión 4", title: "Fabricación Física & FabCoins", desc: "Generación del archivo de fabricación (G-Code) y canje de FabCoins." }
    ],
    image: "images/talleres_niños.jfif",
    instructor: "Equipo Maker Kids & tinkiLab"
  },
  {
    id: "guardianes-del-futuro",
    title: "Guardianes del Futuro: Neo-Artesanía & Lab en Museo",
    subtitle: "Reinterpreta el patrimonio cultural peruano (Moche, Paracas, Inca) con corte láser e impresión 3D",
    category: "patrimonio",
    targetAudience: "Niños (8-14 años) y Familias",
    badge: "Alianza MNAAHP",
    tagColor: "amber",
    price: "S/. 160",
    startDate: "Sábado 25 de Octubre, 2026",
    duration: "4 sesiones prácticas",
    schedule: "Sábados de 3:00 pm a 5:30 pm",
    format: "Presencial en Museo / Híbrido",
    fabTool: "Corte Láser, Vinilo & Impresión 3D 2.5D",
    includes: [
      "Recorridos exploratorios por salas del museo",
      "Materiales incluidos (MDF, acrílicos, filamentos ecológicos)",
      "Creación de tocados tipo Señor de Sipán y rompecabezas ensamblables",
      "Certificado conjunto MNAAHP & FAB LAB Perú"
    ],
    challenge: "Digitalizar patrones prehispánicos ancestrales y fabricar un objeto de neo-artesanía contemporáneo.",
    description: "Transformamos la visita tradicional al museo en un laboratorio de creación. En colaboración con el Museo Nacional de Arqueología, Antropología e Historia del Perú, los participantes analizan la geometría ancestral y la traducen a piezas contemporáneas usando fabricación digital.",
    syllabus: [
      { session: "Sesión 1", title: "Exploración Iconográfica Ancestral", desc: "Identificación de patrones geométricos en textiles y cerámica Moche/Paracas." },
      { session: "Sesión 2", title: "Vectorización & Corte Láser", desc: "Digitalización de siluetas y corte de máscaras/tocados en cartulina estructural y MDF." },
      { session: "Sesión 3", title: "Relieves 3D & Simbología", desc: "Modelado de sellos y posavasos con iconografía en relieve para impresión 3D." },
      { session: "Sesión 4", title: "Exposición de Neo-Artesanía", desc: "Montaje de los proyectos de los participantes y entrega de creaciones físicas." }
    ],
    image: "images/2e6ea2a9-ab0a-40bb-b4d5-df0cae259689.jfif",
    instructor: "FAB LAB Perú + Mediadores Culturales MNAAHP"
  },
  {
    id: "mini-telares-laser",
    title: "Mini Telares Láser: Geometría & Textil Maker",
    subtitle: "Aprende el arte textil andino ensamblando tu propio mini-telar ergonómico de precisión cortado en acrílico",
    category: "patrimonio",
    targetAudience: "Niños desde 6 años, educadores y familias",
    badge: "STEAM & Arte",
    tagColor: "pink",
    price: "S/. 95",
    startDate: "Domingo 19 de Octubre, 2026",
    duration: "1 sesión intensiva (2 horas)",
    schedule: "Domingos de 11:00 am a 1:00 pm",
    format: "Presencial / Kit a domicilio",
    fabTool: "Telar Didáctico Láser de Acrílico (5mm) + Hilos",
    includes: [
      "Kit físico de Mini Telar de Acrílico cortado con láser",
      "Set de hilos de colores y urdimbre",
      "Guía visual de patrones geométricos andinos",
      "Diploma de Pequeño Tejedor Maker"
    ],
    challenge: "Ensamblar las piezas del mini-telar bajo presión, instalar la urdimbre y tejer tu primer tapiz geométrico.",
    description: "Una experiencia que une el diseño paramétrico en corte láser con la sabiduría milenaria del tejido andino. Los niños arman su propio telar de acrílico de fácil ensamblaje y desarrollan motricidad fina, cálculo geométrico y apreciación cultural.",
    syllabus: [
      { session: "Paso 1", title: "Montaje del Telar de Acrílico", desc: "Ensamblaje intuitivo por encajes a presión de las piezas cortadas con láser." },
      { session: "Paso 2", title: "Instalación de la Urdimbre", desc: "Colocación de hilos guía y cálculo de tensiones para el tejido." },
      { session: "Paso 3", title: "Técnicas de Tejido & Patrones", desc: "Creación de franjas de colores y acabado final de la pieza textil." }
    ],
    image: "images/804b30bd-bb28-46e5-a0f9-c84aa255f777.jfif",
    instructor: "Especialistas en Diseño Textil & Fabricación Digital"
  },
  {
    id: "open-circuits",
    title: "Open Circuits: Hardware Libre & Escaneo 3D",
    subtitle: "Digitaliza el mundo físico con escáneres ópticos e interactúa mediante sensores y microcontroladores",
    category: "jovenes",
    targetAudience: "Jóvenes de 14 a 25 años, estudiantes de tecnología y makers",
    badge: "Hardware & 3D Scan",
    tagColor: "indigo",
    price: "S/. 190",
    startDate: "Miércoles 22 de Octubre, 2026",
    duration: "4 sesiones prácticas",
    schedule: "Miércoles y Viernes de 5:00 pm a 7:00 pm",
    format: "Híbrido / Laboratorio en vivo",
    fabTool: "Escáner 3D Óptico/Láser + Placas de Desarrollo",
    includes: [
      "Prácticas directas con escáner 3D de alta precisión",
      "Kit conceptual de componentes electrónicos y esquemáticos",
      "Códigos base para lectura de sensores y respuesta LED",
      "Certificado oficial FAB LAB Perú"
    ],
    challenge: "Escanear una pieza real, corregir su malla 3D y fabricarle una carcasa personalizada con circuito integrado.",
    description: "Aprende a capturar geometría del mundo real usando escaneo 3D y crea dispositivos inteligentes integrando sensores, actuadores y lógica de control. Ideal para jóvenes que quieren dar el salto a la ingeniería de producto.",
    syllabus: [
      { session: "Sesión 1", title: "Electrónica Maker & Circuitos", desc: "Lectura de señales analógicas y digitales con microcontroladores." },
      { session: "Sesión 2", title: "Captura de Malla con Escáner 3D", desc: "Calibración, escaneo por luz estructurada y generación de nube de puntos." },
      { session: "Sesión 3", title: "Ingeniería Inversa", desc: "Reparación de mallas en software 3D y diseño de carcasas inteligentes." },
      { session: "Sesión 4", title: "Puesta en Marcha del Dispositivo", desc: "Ensamble del circuito y pruebas de respuesta en tiempo real." }
    ],
    image: "images/talleres_adolescentes.jfif",
    instructor: "Hayashi & Franchi (Makers e Investigadores)"
  },
  {
    id: "biolaser",
    title: "BioLáser: Biomateriales Orgánicos & Corte Láser",
    subtitle: "Sintetiza bioplásticos a partir de residuos orgánicos y procésalos con tecnología láser de alta precisión",
    category: "jovenes",
    targetAudience: "Jóvenes, diseñadores, arquitectos e innovadores sostenibles",
    badge: "Eco-Innovación",
    tagColor: "teal",
    price: "S/. 220",
    startDate: "Sábado 25 de Octubre, 2026",
    duration: "4 sesiones de laboratorio",
    schedule: "Sábados de 3:30 pm a 6:00 pm",
    format: "Teórico-Práctico con Laboratorio de Muestras",
    fabTool: "Cortadora Láser CO2 + Bio-Polímeros",
    includes: [
      "Recetario exclusivo de formulación de biomateriales",
      "Guía técnica de parámetros láser (potencia, velocidad, frecuencia)",
      "Muestrario digital de texturas y patrones orgánicos",
      "Certificado en Bio-Fabricación y Economía Circular"
    ],
    challenge: "Formular una lámina de biomaterial flexible o rígido y cortarla con láser para un producto utilitario.",
    description: "Explora la frontera entre biología, diseño y tecnología. Aprende a crear materiales sostenibles a partir de almidones, algas y desechos agrícolas, y descubre cómo transformarlos con precisión milimétrica en la cortadora láser.",
    syllabus: [
      { session: "Sesión 1", title: "Bio-Polímeros & Síntesis", desc: "Formulación de bioplásticos biodegradables y selección de aditivos naturales." },
      { session: "Sesión 2", title: "Curado, Texturas y Flexibilidad", desc: "Control de secado, resistencia mecánica y ensayos de rigidez." },
      { session: "Sesión 3", title: "Corte Láser en Bio-Sustratos", desc: "Cálculo de vectorizado y calibración para evitar la combustión del material orgánico." },
      { session: "Sesión 4", title: "Prototipo Final Sostenible", desc: "Ensamble por encastre sin adhesivos contaminantes y exhibición." }
    ],
    image: "images/4b6c4ba0-3377-40c8-b924-54d58168cfbf.jfif",
    instructor: "Grace, Carmen, David & Miguel (Equipo BioLab)"
  },
  {
    id: "laser-and-layers",
    title: "Láser & Layers: Stencils & Arte en MDF / Cartulina",
    subtitle: "Domina el corte láser multicapa, preparación vectorial y diseño de patrones artísticos en relieve",
    category: "adultos",
    targetAudience: "Adultos, artistas visuales, diseñadores gráficos y arquitectos",
    badge: "Artes Gráficas & Diseño",
    tagColor: "violet",
    price: "S/. 180",
    startDate: "Jueves 23 de Octubre, 2026",
    duration: "3 sesiones intensivas",
    schedule: "Jueves de 7:00 pm a 9:30 pm",
    format: "Virtual en vivo con laboratorio láser interactivo",
    fabTool: "Cortadora Láser CO2 de Gran Formato",
    includes: [
      "Banco vectorial exclusivo de patrones geométricos",
      "Manual técnico de tolerancias (Kerf), puentes para stencils y grabado",
      "Corte en vivo con cámara HD transmitiendo desde el cabezal láser",
      "Certificado oficial FAB LAB Perú"
    ],
    challenge: "Diseñar un cuadro tridimensional en capas ensambladas o una plantilla de stencil profesional.",
    description: "Aprende el flujo profesional de vectorización para máquinas de corte láser. Domina la creación de puentes para tipografías caladas y la técnica de capas volumétricas (*layers*) en cartulinas finas, microcorrugado y madera MDF.",
    syllabus: [
      { session: "Sesión 1", title: "Diseño Vectorial para Láser", desc: "Nodos, puentes de retención para stencils y cálculo de compensación de rayo (Kerf)." },
      { session: "Sesión 2", title: "Corte y Grabado en Múltiples Materiales", desc: "Parámetros óptimos para papel, cartulinas de alto gramaje y tableros de MDF." },
      { session: "Sesión 3", title: "Composición 3D por Capas", desc: "Técnicas de superposición, adhesión limpia y acabados con pintura." }
    ],
    image: "images/talleres_adultos.jfif",
    instructor: "Evelyn, Cristian & Silvana (Láser Specialists)"
  },
  {
    id: "fablab-docentes",
    title: "STEAM & FabLab Maker para Docentes",
    subtitle: "Formación pedagógica para educadores: integra impresión 3D, corte láser y robótica en el aula",
    category: "instituciones",
    targetAudience: "Docentes de Ciencias, Arte, Matemáticas y Directivos Escolares",
    badge: "Formación Docente",
    tagColor: "emerald",
    price: "S/. 250",
    startDate: "Sábado 1 de Noviembre, 2026",
    duration: "4 sesiones (12 horas pedagógicas)",
    schedule: "Sábados de 9:00 am a 12:00 pm",
    format: "Híbrido (Virtual + Taller Presencial)",
    fabTool: "Laboratorio STEAM Integral",
    includes: [
      "Guías pedagógicas listas para aplicar en el aula por niveles",
      "Malla curricular basada en retos STEAM y metodología MIT",
      "Uso de software educativo gratuito y accesible",
      "Certificación pedagógica con valor curricular"
    ],
    challenge: "Diseñar una unidad didáctica integradora donde los alumnos resuelvan un problema real fabricando un prototipo.",
    description: "Capacitamos a los profesores para transformar sus clases tradicionales en experiencias prácticas de innovación. Aprenderán a formular proyectos escolares utilizando impresoras 3D, cortadoras láser y electrónica básica sin necesidad de conocimientos previos avanzados.",
    syllabus: [
      { session: "Módulo 1", title: "Metodología FabLab en la Educación", desc: "El rol del docente facilitador y el aprendizaje basado en proyectos (PBL)." },
      { session: "Módulo 2", title: "Diseño y Fabricación 3D en el Aula", desc: "Software intuitivo para escolares y proyectos aplicados a ciencias y matemáticas." },
      { session: "Módulo 3", title: "Corte Láser & Recursos Didácticos", desc: "Creación de maquetas, juegos de memoria y material concreto para clases." },
      { session: "Módulo 4", title: "Evaluación de Competencias STEAM", desc: "Rúbricas de evaluación del pensamiento de diseño e innovación estudiantil." }
    ],
    image: "images/talleres_docentes.jfif",
    instructor: "Red de Innovación Educativa FAB LAB Perú"
  }
];
