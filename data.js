// Datos de Talleres y Programas - FAB LAB Perú (edu.fab.pe)
// Número de atención actualizado y contenidos simplificados

const SITE_DATA = {
  phone: "+51 989 984 480",
  whatsappBase: "51989984480",
  brandName: "FAB LAB Perú",
  brandTagline: "Innovación y Fabricación Digital",
  mainSiteUrl: "https://fab.pe"
};

const CATEGORIES = [
  { id: "all", name: "Todos los Talleres" },
  { id: "personas", name: "Para Personas & Jóvenes" },
  { id: "cultura", name: "Diseño & Cultura" },
  { id: "instituciones", name: "Para Colegios & Empresas" }
];

const WORKSHOPS = [
  {
    id: "diseno-impresion-3d",
    title: "Diseño y Fabricación en Impresión 3D",
    subtitle: "Aprende a transformar bocetos e ideas en objetos físicos funcionales mediante modelado y fabricación 3D.",
    category: "personas",
    targetAudience: "Niños (8+), Jóvenes y Principiantes",
    badge: "Inicio Rápido",
    price: "S/. 150",
    startDate: "Sábado 18 de Octubre",
    duration: "4 sesiones prácticas (6 hrs)",
    schedule: "Sábados de 10:00 am a 11:30 am",
    format: "Virtual en vivo + Asesoría de impresión",
    fabTool: "Modelado 3D & Impresora FDM",
    description: "Un taller introductorio y 100% práctico para aprender a pensar en tres dimensiones. Diseñarás piezas funcionales, comprenderás cómo preparar archivos para máquinas de impresión 3D y fabricarás tu propio prototipo desde casa con acompañamiento experto.",
    highlights: [
      "Aprende software de modelado 3D intuitivo y gratuito.",
      "Comprende tolerancias, rellenos y parámetros de impresión.",
      "Materializa una pieza propia lista para uso real.",
      "Certificado de participación emitido por FAB LAB Perú."
    ],
    image: "images/talleres_niños.jfif"
  },
  {
    id: "corte-laser-diseno",
    title: "Corte y Grabado Láser: Del Vector al Producto",
    subtitle: "Domina el diseño vectorial y aprende a fabricar piezas por encastre, stencils y cuadros en relieve.",
    category: "personas",
    targetAudience: "Jóvenes, Diseñadores, Creativos y Adultos",
    badge: "Práctico & Visual",
    price: "S/. 180",
    startDate: "Jueves 23 de Octubre",
    duration: "3 sesiones intensivas (7 hrs)",
    schedule: "Jueves de 7:00 pm a 9:15 pm",
    format: "Virtual en vivo con laboratorio láser interactivo",
    fabTool: "Diseño Vectorial & Cortadora Láser CO2",
    description: "Aprende el flujo de trabajo profesional para máquinas láser. Desde cómo trazar vectores limpios sin errores hasta el corte preciso y grabado de superficies en cartulinas, acrílico y madera MDF para empaques, arte gráfico o maquetas.",
    highlights: [
      "Preparación de vectores y optimización de curvas y nodos.",
      "Cálculo de encajes por presión sin pegamentos.",
      "Técnicas de superposición de capas en relieve (layers).",
      "Demostración técnica en tiempo real desde el laboratorio."
    ],
    image: "images/talleres_adultos.jfif"
  },
  {
    id: "patrimonio-neoartesania",
    title: "Innovación y Patrimonio: Neo-Artesanía Digital",
    subtitle: "Reinterpreta la iconografía y patrones andinos/prehispánicos con herramientas de fabricación digital.",
    category: "cultura",
    targetAudience: "Familias, Jóvenes, Artistas y Educadores",
    badge: "Arte & Identidad",
    price: "S/. 160",
    startDate: "Sábado 25 de Octubre",
    duration: "4 sesiones (8 hrs)",
    schedule: "Sábados de 3:30 pm a 5:30 pm",
    format: "Presencial / Híbrido",
    fabTool: "Corte Láser & Relieves en Impresión 3D",
    description: "Una experiencia que une el acervo cultural milenario del Perú con tecnologías modernas. Aprenderás a extraer geometría de cerámicas y textiles para crear piezas contemporáneas: rompecabezas ensamblables, posavasos en relieve y accesorios utilitarios.",
    highlights: [
      "Digitalización de patrones geométricos prehispánicos.",
      "Fabricación de piezas utilitarias con identidad propia.",
      "Uso responsable de materiales sostenibles.",
      "Exhibición y constancia con respaldo cultural."
    ],
    image: "images/2e6ea2a9-ab0a-40bb-b4d5-df0cae259689.jfif"
  },
  {
    id: "electronica-sensores",
    title: "Circuitos & Prototipado Interactivo",
    subtitle: "Aprende a integrar sensores, luces y actuadores para crear dispositivos y proyectos interactivos.",
    category: "personas",
    targetAudience: "Jóvenes de 14 a 25 años y curiosos tecnológicos",
    badge: "Hardware Fácil",
    price: "S/. 190",
    startDate: "Miércoles 22 de Octubre",
    duration: "4 sesiones guiadas (8 hrs)",
    schedule: "Miércoles y Viernes de 5:00 pm a 7:00 pm",
    format: "Híbrido / Virtual interactivo",
    fabTool: "Microcontroladores, Sensores & Escaneo 3D",
    description: "Descubre cómo dar vida e interactividad a objetos cotidianos. Aprenderás conceptos básicos de electrónica práctica, conexión de sensores de proximidad o luz y cómo diseñar carcasas a medida para proteger tus circuitos.",
    highlights: [
      "Conexión de sensores analógicos y digitales sin complicaciones.",
      "Conceptos clave de lógica interactiva y automatización.",
      "Digitalización de piezas físicas mediante escaneo 3D.",
      "Asesoría directa con mentores de desarrollo de producto."
    ],
    image: "images/talleres_adolescentes.jfif"
  },
  {
    id: "laboratorios-escolares",
    title: "Innovación y Espacios Maker para Colegios",
    subtitle: "Acompañamiento integral para instituciones educativas que buscan implementar experiencias prácticas STEAM.",
    category: "instituciones",
    targetAudience: "Colegios, Directores, Coordinadores y Docentes",
    badge: "Solución Escolar",
    price: "Propuesta a medida",
    startDate: "Inicio coordinado con la institución",
    duration: "Talleres modulares o anuales",
    schedule: "Horarios adaptables a la jornada escolar",
    format: "Presencial en el colegio o en nuestras sedes",
    fabTool: "Espacios de Fabricación Digital & Metodología STEAM",
    description: "Diseñamos programas educativos y formativos a la medida de tu centro educativo. Brindamos capacitación a los profesores y talleres dinámicos para estudiantes, fomentando la resolución de problemas reales a través de proyectos tecnológicos tangibles.",
    highlights: [
      "Capacitación docente en aprendizaje basado en proyectos prácticos.",
      "Talleres extracurriculares adaptados a distintas edades.",
      "Asesoría técnica en equipamiento seguro y adecuado para aulas.",
      "Certificación institucional y seguimiento continuo."
    ],
    image: "images/talleres_docentes.jfif"
  },
  {
    id: "innovacion-empresas",
    title: "Experiencias de Innovación & Prototipado para Empresas",
    subtitle: "Jornadas inmersivas de co-creación y agilidad donde los equipos pasan del concepto al prototipo físico.",
    category: "instituciones",
    targetAudience: "Empresas, Equipos de Innovación, TI y Recursos Humanos",
    badge: "Corporate Maker",
    price: "Propuesta a medida",
    startDate: "Fechas a solicitud corporativa",
    duration: "Half-Day (4 hrs) o Full-Day (8 hrs)",
    schedule: "A convenir con la empresa",
    format: "Presencial en laboratorio o sede corporativa",
    fabTool: "Prototipado Rápido & Design Sprint",
    description: "Sal de la rutina y fortalece la capacidad de ideación de tu equipo. A través de desafíos guiados y herramientas de fabricación rápida, los participantes colaboran bajo una mentalidad ágil para materializar soluciones a retos específicos de la organización.",
    highlights: [
      "Dinámicas de colaboración y pensamiento experimental.",
      "Creación de prototipos tangibles durante la misma jornada.",
      "Facilitadores experimentados en metodologías de innovación.",
      "Espacio inspirador equipado con tecnología de vanguardia."
    ],
    image: "images/4b6c4ba0-3377-40c8-b924-54d58168cfbf.jfif"
  }
];
