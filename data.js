// Datos de Talleres Abiertos - FAB LAB Perú (edu.fab.pe)
// Redacción orientada a la acción, experimentación y creación práctica

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

const WORKSHOPS = [
  {
    id: "diseno-impresion-3d",
    title: "Modelado 3D & Prototipado Físico",
    subtitle: "Materializa objetos desde tu computadora: modela en tres dimensiones y fabrica tus propias piezas en impresión 3D.",
    category: "kids",
    targetAudience: "Niños y Adolescentes (8 a 15 años)",
    badge: "3D & Fabricación",
    price: "S/. 150",
    startDate: "Sábado 18 de Octubre",
    duration: "4 sesiones prácticas (6 hrs)",
    schedule: "Sábados de 10:00 am a 11:30 am",
    format: "Virtual interactivo + Fabricación de pieza final",
    fabTool: "Software de Diseño 3D & Impresora FDM",
    description: "Una experiencia inmersiva para explorar el pensamiento espacial y la geometría aplicada. Proyectarás tus propias ideas en software 3D, descubrirás cómo calibrar parámetros mecánicos y verás tu creación materializarse como un objeto físico funcional.",
    highlights: [
      "Domina herramientas amigables de modelado tridimensional.",
      "Comprende el comportamiento de materiales y capas de impresión.",
      "Resuelve retos de encajes y tolerancias mecánicas.",
      "Te llevas tu pieza terminada fabricada en el laboratorio."
    ],
    image: "images/talleres_niños.jfif"
  },
  {
    id: "corte-laser-layers",
    title: "Corte Láser & Composición Multicapa",
    subtitle: "Explora la precisión del rayo láser para crear ensambles, piezas por relieve y stencils sobre madera y cartulinas.",
    category: "creativos",
    targetAudience: "Jóvenes, Diseñadores y Curiosos",
    badge: "Precisión Láser",
    price: "S/. 180",
    startDate: "Jueves 23 de Octubre",
    duration: "3 sesiones intensivas (7 hrs)",
    schedule: "Jueves de 7:00 pm a 9:15 pm",
    format: "Virtual en vivo con laboratorio láser en directo",
    fabTool: "Diseño Vectorial & Cortadora Láser CO2",
    description: "Trabaja con la herramienta de corte más rápida y versátil del mundo maker. Transforma vectores digitales en piezas volumétricas, cuadros decorativos en capas de MDF, plantillas caladas y ensamblajes limpios sin adhesivos.",
    highlights: [
      "Optimiza curvas, nodos y vectores para máquinas de alta potencia.",
      "Experimenta con cartulinas de alto gramaje, MDF y acrílicos.",
      "Diseña sistemas de ensamblaje por presión (press-fit).",
      "Demostración técnica de corte transmitida en alta definición."
    ],
    image: "images/talleres_adultos.jfif"
  },
  {
    id: "patrimonio-neoartesania",
    title: "Neo-Artesanía: Geometría Ancestral & Tecnología",
    subtitle: "Conecta la iconografía prehispánica con la fabricación moderna: crea sellos, relieves y piezas utilitarias.",
    category: "profesionales",
    targetAudience: "Adultos, Familias, Artistas y Docentes",
    badge: "Cultura & Diseño",
    price: "S/. 160",
    startDate: "Sábado 25 de Octubre",
    duration: "4 sesiones (8 hrs)",
    schedule: "Sábados de 3:30 pm a 5:30 pm",
    format: "Presencial / Híbrido",
    fabTool: "Vectorizado Láser & Relieves en 3D",
    description: "Reinterpreta el legado visual de las culturas peruanas mediante herramientas del siglo XXI. Digitalizarás patrones textiles y geométricos ancestrales para transformarlos en rompecabezas ensamblables, posavasos en relieve y piezas decorativas contemporáneas.",
    highlights: [
      "Extrae y digitaliza patrones geométricos milenarios.",
      "Combina corte láser y relieves tridimensionales.",
      "Crea productos utilitarios con identidad cultural viva.",
      "Constancia de participación emitida por FAB LAB Perú."
    ],
    image: "images/2e6ea2a9-ab0a-40bb-b4d5-df0cae259689.jfif"
  },
  {
    id: "electronica-sensores",
    title: "Hardware Interactivo & Escaneo 3D",
    subtitle: "Integra circuitos, sensores y digitalización 3D para construir dispositivos inteligentes que interactúan con su entorno.",
    category: "creativos",
    targetAudience: "Jóvenes de 14 a 25 años y Estudiantes",
    badge: "Hardware & Código",
    price: "S/. 190",
    startDate: "Miércoles 22 de Octubre",
    duration: "4 sesiones guiadas (8 hrs)",
    schedule: "Miércoles y Viernes de 5:00 pm a 7:00 pm",
    format: "Híbrido / Laboratorio interactivo",
    fabTool: "Microcontroladores, Sensores & Escáner 3D",
    description: "Pasa de ser usuario de tecnología a ser creador de hardware. Conecta sensores de presencia, luz y movimiento a placas programables, digitaliza geometrías con escáneres 3D y crea carcasas personalizadas para tus dispositivos.",
    highlights: [
      "Conecta y programa sensores sin rodeos teóricos innecesarios.",
      "Digitaliza objetos del mundo real mediante escáneres 3D.",
      "Fabrica gabinetes a medida adaptados a tu circuito.",
      "Asesoría directa para prototipos funcionales."
    ],
    image: "images/talleres_adolescentes.jfif"
  },
  {
    id: "biomateriales-laser",
    title: "Bio-Fabricación: Materiales Orgánicos & Láser",
    subtitle: "Sintetiza bio-plásticos a partir de recursos orgánicos y experimenta con corte y grabado sostenible.",
    category: "profesionales",
    targetAudience: "Jóvenes, Arquitectos, Diseñadores e Innovadores",
    badge: "Eco-Innovación",
    price: "S/. 220",
    startDate: "Sábado 25 de Octubre",
    duration: "4 sesiones experimentales (9 hrs)",
    schedule: "Sábados de 3:30 pm a 5:45 pm",
    format: "Laboratorio Teórico-Práctico",
    fabTool: "Bio-Polímeros & Cortadora Láser CO2",
    description: "Explora la convergencia entre biología, sostenibilidad y fabricación digital. Aprende recetas para sintetizar láminas de biomateriales flexibles o rígidos a partir de residuos orgánicos y transfórmalas con corte láser en objetos de diseño circular.",
    highlights: [
      "Formulación de bioplásticos biodegradables y aditivos naturales.",
      "Pruebas mecánicas de elasticidad, textura y secado.",
      "Corte y grabado láser calibrado para sustratos orgánicos.",
      "Creación de prototipos ecológicos sin pegamentos químicos."
    ],
    image: "images/4b6c4ba0-3377-40c8-b924-54d58168cfbf.jfif"
  },
  {
    id: "mini-telares-textil",
    title: "Mini-Telares Láser & Geometría Textil",
    subtitle: "Ensambla tu propio telar de acrílico cortado con láser y teje patrones geométricos basados en la tradición andina.",
    category: "kids",
    targetAudience: "Niños (desde 6 años), Familias y Educadores",
    badge: "Arte & Ensamblaje",
    price: "S/. 95",
    startDate: "Domingo 19 de Octubre",
    duration: "1 sesión intensiva (2 hrs)",
    schedule: "Domingos de 11:00 am a 1:00 pm",
    format: "Presencial / Kit a domicilio",
    fabTool: "Telar Didáctico de Acrílico (5mm) + Hilos",
    description: "Una dinámica donde la fabricación digital se une con el arte textil. Los participantes arman un telar ergonómico de acrílico diseñado por encajes precisos, colocan la urdimbre y desarrollan su primer tapiz geométrico.",
    highlights: [
      "Montaje por encajes a presión de piezas cortadas con láser.",
      "Desarrollo de motricidad fina y cálculo geométrico.",
      "Kit completo de telar e hilos de colores incluido.",
      "Te llevas tu mini-telar y tu tapiz terminado."
    ],
    image: "images/804b30bd-bb28-46e5-a0f9-c84aa255f777.jfif"
  }
];
