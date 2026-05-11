# 4. DESENVOLVIMENTO

Neste capítulo, será abordado o desenvolvimento do sistema web de agendamento clínico, solução proposta para atender ao problema apresentado nesta pesquisa. A aplicação foi concebida para organizar, em um único ambiente digital, as rotinas de cadastro, marcação de consultas, acompanhamento da agenda, confirmação de atendimentos, gestão dos profissionais de saúde e controle administrativo da clínica. Dessa forma, busca-se reduzir falhas comuns em processos manuais, como conflitos de horário, dificuldade de acompanhamento dos atendimentos, falta de padronização no cadastro dos pacientes e ausência de rastreabilidade das ações executadas no sistema.

Com esse propósito, o desenvolvimento concentrou-se em uma aplicação web estruturada para funcionar como ferramenta de apoio à operação clínica, permitindo que diferentes perfis de usuários utilizem o sistema conforme suas responsabilidades. O projeto foi implementado com Laravel 12, PHP 8.2, banco de dados MySQL, Blade para construção das interfaces, além de recursos complementares de JavaScript, Bootstrap, jQuery, Vite e bibliotecas auxiliares voltadas para calendário, notificações e interação visual. A arquitetura adotada favorece a separação entre regras de negócio, acesso aos dados e apresentação das telas, contribuindo para a manutenção, expansão e organização da solução.

4\.1. Requisitos

Buscando desenvolver uma aplicação web robusta, fez-se necessário o levantamento de requisitos, responsável por detalhar as necessidades técnicas para o funcionamento pleno do sistema, bem como as funcionalidades essenciais para a sua utilização pelos usuários. Dessa forma, foram definidos requisitos funcionais e não funcionais, com base nas necessidades observadas na rotina de uma clínica e nos objetivos estabelecidos para a informatização do processo de agendamento. Para melhor organização, adotou-se a convenção RF- para requisitos funcionais e RNF- para requisitos não funcionais, ambos com identificadores numéricos sequenciais.

4\.1\.1. Requisitos Funcionais

Como mencionado, os requisitos funcionais determinam o que deve existir no software em termos de ações, comportamentos e funções. A seguir, são apresentados os principais requisitos funcionais identificados para o sistema.

Quadro 01 - Requisitos Funcionais

| Identificador | Descrição |
| --- | --- |
| RF-01 | Autenticação e controle de acesso por perfil |
| RF-02 | Cadastro, edição, visualização e inativação de pacientes |
| RF-03 | Validação de duplicidade de pacientes |
| RF-04 | Cadastro e gerenciamento de profissionais de saúde |
| RF-05 | Configuração do horário de funcionamento da clínica |
| RF-06 | Configuração de vínculos de agenda dos profissionais |
| RF-07 | Cadastro e manutenção de procedimentos |
| RF-08 | Criação, edição e cancelamento de agendamentos |
| RF-09 | Validação de disponibilidade de horário da clínica e do profissional |
| RF-10 | Exibição de agendamentos na agenda geral e no calendário |
| RF-11 | Controle de confirmações, pendências e cancelamentos |
| RF-12 | Gerenciamento da fila de espera e dos atendimentos em atraso |
| RF-13 | Finalização de atendimento e consulta ao histórico |
| RF-14 | Gerenciamento de usuários, permissões e logs de atividade |
| RF-15 | Edição da própria conta e consulta de notificações |

Fonte: Autor (2026).

• RF-01: Garante autenticação dos usuários e controle de acesso conforme o perfil cadastrado no sistema. Essa funcionalidade é necessária para restringir ações sensíveis e permitir que administrador, recepcionista, profissional e gestor da clínica visualizem apenas os módulos compatíveis com suas responsabilidades.

• RF-02: Possibilita cadastrar, editar, listar, visualizar e inativar pacientes. Esse requisito é fundamental porque o cadastro do paciente serve como base para a abertura e o acompanhamento dos agendamentos realizados pela clínica.

• RF-03: Estabelece validações para evitar duplicidade de pacientes por CPF, e-mail e telefone. Essa regra melhora a consistência dos dados e reduz problemas operacionais causados por registros repetidos no sistema.

• RF-04: Permite cadastrar e gerenciar profissionais de saúde, vinculando-os a usuários do sistema e registrando informações como especialidade principal, conselho profissional, número de registro e cor de identificação da agenda.

• RF-05: Permite configurar o horário geral de funcionamento da clínica, incluindo horários de abertura, encerramento e intervalo interno. Esse requisito é utilizado como referência para as validações dos agendamentos e das disponibilidades dos profissionais.

• RF-06: Possibilita definir a agenda de atuação dos profissionais por dia da semana, dentro da janela de atendimento da clínica. Essa funcionalidade ajuda a organizar a disponibilidade semanal e evita cadastros de horários incompatíveis com o funcionamento da unidade.

• RF-07: Permite cadastrar, editar, ativar, desativar e excluir procedimentos ou serviços prestados pela clínica, armazenando informações como nome, duração padrão e profissional responsável pelo atendimento.

• RF-08: Corresponde ao núcleo operacional do sistema, permitindo criar, editar, visualizar e cancelar agendamentos. Nesse fluxo, o usuário seleciona paciente, profissional, procedimento, data e horário do atendimento.

• RF-09: Determina que, antes da gravação do agendamento, o sistema deve validar a disponibilidade do profissional, o horário de funcionamento da clínica e o intervalo configurado. Essa validação reduz conflitos de agenda e inconsistências na operação diária.

• RF-10: Possibilita a exibição dos agendamentos tanto em formato de agenda geral quanto em calendário visual. Com isso, o sistema oferece uma visualização complementar dos compromissos, facilitando consultas, filtros e acompanhamento por período.

• RF-11: Permite organizar os agendamentos conforme o seu estado operacional, possibilitando confirmar, manter pendente ou cancelar atendimentos. Esse requisito contribui para o controle da rotina da clínica e para a atualização do status dos compromissos.

• RF-12: Garante a existência de funcionalidades específicas para fila de espera e atendimentos em atraso. Com isso, o profissional consegue acompanhar casos que exigem ação imediata, separando os atendimentos em execução daqueles que já ultrapassaram o horário previsto de término.

• RF-13: Possibilita concluir atendimentos e enviá-los ao histórico finalizado. Essa funcionalidade preserva o registro operacional da consulta e permite que os usuários consultem posteriormente os atendimentos já encerrados.

• RF-14: Permite gerenciar usuários, permissões de acesso por submenu e logs de atividade. Esse requisito é importante para a administração do sistema, pois garante controle sobre quem pode executar determinada ação e registra o histórico das alterações realizadas.

• RF-15: Permite ao usuário editar a própria conta, atualizar informações pessoais e consultar notificações relacionadas à rotina do sistema. Essa funcionalidade melhora a autonomia do usuário e centraliza avisos operacionais importantes.

4\.1\.2. Requisitos Não Funcionais

Como mencionado, os requisitos não funcionais definem condições necessárias para que o software funcione com segurança, desempenho, confiabilidade, usabilidade e capacidade de manutenção. A seguir, são apresentados os principais requisitos não funcionais identificados para a aplicação.

Quadro 02 - Requisitos Não Funcionais

| Identificador | Descrição |
| --- | --- |
| RNF-01 | Interface web responsiva para desktop e dispositivos móveis |
| RNF-02 | Controle de acesso baseado em autenticação, perfil e permissões |
| RNF-03 | Preservação da integridade dos dados de agendamento |
| RNF-04 | Registro de trilha de auditoria para ações relevantes |
| RNF-05 | Utilização de banco de dados relacional MySQL |
| RNF-06 | Utilização do português do Brasil como idioma principal |
| RNF-07 | Desempenho adequado nas consultas de agenda e histórico |
| RNF-08 | Estrutura modular para facilitar manutenção e evolução |
| RNF-09 | Compatibilidade com ambiente local de desenvolvimento em XAMPP |
| RNF-10 | Persistência segura de arquivos enviados pelos usuários |
| RNF-11 | Padrão visual consistente e navegação clara entre os módulos |
| RNF-12 | Extensibilidade para novos módulos futuros |

Fonte: Autor (2026).

• RNF-01: O sistema deve possuir interface responsiva, permitindo uso adequado em computadores, notebooks, tablets e dispositivos móveis. Esse requisito melhora a acessibilidade da solução em diferentes contextos de uso.

• RNF-02: A autenticação e o controle de acesso devem ser realizados com base em login, perfil de usuário e permissões configuradas. Esse requisito reforça a segurança e limita a execução de ações conforme a responsabilidade de cada ator no sistema.

• RNF-03: O sistema deve preservar a integridade dos dados de agendamento, impedindo conflitos de horário, inconsistências de agenda e registros incompatíveis com as regras de negócio estabelecidas.

• RNF-04: As operações administrativas e operacionais relevantes devem ser registradas em logs de atividade. Essa exigência favorece auditoria, rastreabilidade e acompanhamento das ações realizadas no ambiente do sistema.

• RNF-05: A persistência dos dados deve ocorrer em banco de dados relacional MySQL, garantindo organização estruturada das informações, integridade referencial e facilidade de consulta.

• RNF-06: O idioma principal da interface deve ser o português do Brasil, adequando a aplicação ao contexto de uso dos usuários previstos e facilitando a compreensão das funcionalidades.

• RNF-07: O sistema deve apresentar desempenho satisfatório nas rotinas de consulta da agenda, calendário, fila de espera e histórico finalizado, evitando lentidão excessiva nas principais operações da clínica.

• RNF-08: O código-fonte deve seguir organização modular e padrão arquitetural que facilite manutenção e evolução. A adoção do modelo MVC contribui para o desacoplamento entre lógica de negócio, dados e interface.

• RNF-09: A aplicação deve ser compatível com execução local em ambiente XAMPP, favorecendo o processo de desenvolvimento, testes e manutenção do sistema durante sua implementação.

• RNF-10: O sistema deve garantir persistência segura de arquivos enviados, como imagens de perfil, assegurando armazenamento adequado e proteção contra perda ou inconsistência desses dados.

• RNF-11: A navegação deve manter padrão visual coerente entre navbar, sidebar, dashboard e módulos internos, facilitando a localização das funcionalidades e reduzindo a complexidade de uso.

• RNF-12: A solução deve ser extensível para permitir evolução futura, incluindo novos recursos como prontuário, prescrições, relatórios e laudos, sem necessidade de reconstrução estrutural do sistema.

4\.2. Diagramas

Para servir como ferramenta visual e auxiliar na compreensão, no planejamento, na comunicação e na documentação da estrutura e do comportamento do software, esta seção é dedicada aos diagramas desenvolvidos para o sistema.

4\.2\.1. Diagrama de Caso de Uso

Servindo de base para uma melhor compreensão, o diagrama de caso de uso representa uma visão geral das funcionalidades da aplicação a partir da perspectiva de quem a utiliza. No presente sistema, os atores considerados foram administrador, recepcionista, profissional e gestor da clínica, cada um com permissões específicas conforme o papel desempenhado dentro da rotina da instituição.

Conforme o diagrama elaborado, o administrador possui maior amplitude de acesso, podendo gerenciar pacientes, profissionais, procedimentos, agendamentos, usuários, permissões e logs. O recepcionista participa principalmente das rotinas de cadastro de pacientes, criação de agendamentos, confirmações e consultas operacionais. O profissional acessa a própria agenda, acompanha a fila de espera, visualiza atendimentos em atraso e realiza a conclusão do atendimento. Já o gestor da clínica atua com foco administrativo e gerencial, especialmente em módulos de configuração e visualização estratégica. Esse diagrama evidencia como o sistema distribui responsabilidades e como as funcionalidades são organizadas de acordo com o perfil de cada usuário.

4\.2\.2. Diagrama Modelo Entidade-Relacionamento (MER)

Com o objetivo de realizar a modelagem de dados, foi desenvolvido o modelo entidade-relacionamento com base nos requisitos funcionais apresentados anteriormente. O diagrama contempla as principais entidades do sistema, entre elas usuário, paciente, profissional, agenda profissional, procedimento, horário da clínica, agendamento e log de atividade.

O modelo oferece uma representação da estrutura lógica do banco de dados, permitindo a identificação clara das entidades, de seus atributos e dos relacionamentos existentes entre elas. Nesse contexto, os usuários podem estar vinculados a profissionais e também são responsáveis por registrar ações no sistema. Os pacientes se relacionam com os agendamentos, enquanto os profissionais possuem agenda própria, executam procedimentos e atendem consultas registradas no banco. O horário da clínica atua como referência para limitar a disponibilidade de atendimento, e os logs de atividade registram ações relevantes realizadas no ambiente da aplicação. Essa modelagem é essencial para garantir consistência, integridade e organização das informações, servindo como base para a implementação física do banco de dados.

4\.2\.3. Diagrama de Atividade

O diagrama de atividade é utilizado para representar graficamente o fluxo de ações e decisões que compõem um processo dentro do sistema. No caso desta pesquisa, foi modelada como principal atividade a rotina de criação e acompanhamento de um agendamento clínico, por se tratar do processo central da solução proposta.

Conforme ilustrado no diagrama, o fluxo se inicia com a seleção do paciente. Caso o paciente ainda não esteja cadastrado, o sistema permite seu cadastro antes da continuidade do processo. Em seguida, o usuário seleciona o profissional responsável pelo atendimento, escolhe o procedimento desejado e informa a data e o horário pretendidos.

Na etapa seguinte, o sistema valida se o horário informado respeita a agenda do profissional e a janela de funcionamento da clínica. Se houver indisponibilidade, conflito de horário ou incompatibilidade com as regras configuradas, o usuário é informado e deve selecionar um novo horário. Caso a validação seja bem-sucedida, o agendamento é salvo com status adequado ao fluxo operacional, sendo exibido na agenda geral e no calendário.

Após essa etapa, o sistema continua acompanhando o ciclo do atendimento. Se o horário final ainda não tiver sido alcançado, o compromisso permanece nas listas operacionais, como confirmações ou fila de espera. Caso o horário previsto já tenha sido ultrapassado sem encerramento, o atendimento pode ser direcionado para a área de atrasos. Quando o profissional realiza o atendimento, o sistema permite sua finalização, registrando o histórico do compromisso e a respectiva ação no log de atividades. Esse fluxo demonstra como o sistema organiza não apenas a criação do agendamento, mas também seu acompanhamento até a conclusão.

4\.3. Prototipação

A prototipação, no contexto desta pesquisa, não foi compreendida apenas como uma etapa de representação visual das telas, mas como um procedimento de apoio à construção da solução proposta para o problema investigado. Considerando que o objetivo do sistema consiste em informatizar a rotina de agendamento clínico, reduzir falhas operacionais e melhorar o controle das informações, a elaboração das interfaces precisou refletir diretamente os requisitos levantados, os fluxos descritos nos diagramas e a organização funcional definida para o ambiente da aplicação. Nesse sentido, a prototipação serviu como elo entre a modelagem teórica do sistema e sua materialização prática.

Com base na documentação produzida ao longo do desenvolvimento, especialmente nos requisitos funcionais e não funcionais, na definição dos perfis de acesso e nos diagramas de uso e atividade, buscou-se estruturar uma interface que favorecesse clareza, sequência lógica de execução e compatibilidade com a rotina real da clínica. Assim, a distribuição dos módulos, a disposição dos formulários, a organização das tabelas, a separação dos estados dos agendamentos e a apresentação dos avisos operacionais foram pensadas como respostas diretas às necessidades identificadas na pesquisa.

Em um primeiro nível, a prototipação concentrou-se na definição da arquitetura de navegação do sistema. Nessa etapa, foram organizados os agrupamentos funcionais que compõem o ambiente administrativo e operacional da aplicação, abrangendo painel inicial, pacientes, agendamentos, painel do profissional, cadastros base, notificações e conta do usuário. Essa estrutura permitiu visualizar a hierarquia entre módulos e submódulos, bem como estabelecer uma navegação coerente por meio da barra superior, do menu lateral e das telas internas associadas a cada processo do sistema.

Em um segundo nível, a prototipação voltou-se às telas centrais da solução, isto é, àquelas diretamente relacionadas ao cadastro dos pacientes, ao gerenciamento da agenda, à confirmação dos atendimentos, ao acompanhamento da atuação dos profissionais e ao controle administrativo da clínica. Nessa fase, a representação das interfaces contribuiu para antecipar o posicionamento dos componentes informacionais, a ordem de preenchimento dos campos e a visibilidade das ações operacionais mais recorrentes. Tal definição foi importante para assegurar que a futura implementação mantivesse coerência com os fluxos previstos na documentação do sistema.

Outro aspecto relevante dessa etapa foi a busca por unidade visual e usabilidade. As telas foram pensadas com padronização de títulos, botões, campos de formulário, tabelas, indicadores e alertas, de forma a manter previsibilidade de uso entre diferentes módulos. Além disso, a preocupação com responsividade e legibilidade reforçou a necessidade de uma solução que pudesse ser utilizada com clareza em diferentes resoluções de tela, respeitando também os requisitos não funcionais definidos anteriormente.

Sob a perspectiva do tema desta pesquisa, a prototipação mostrou-se particularmente importante no fluxo de agendamento, por se tratar do processo central da solução proposta. A organização prévia das etapas de seleção do paciente, escolha do profissional, definição do procedimento, validação de data e horário e acompanhamento posterior do atendimento permitiu transformar regras de negócio em elementos visuais compreensíveis. Da mesma forma, a representação das áreas específicas do profissional, como calendário próprio, fila de espera e atendimentos em atraso, contribuiu para adequar a interface ao contexto real de uso de cada perfil.

Desse modo, a prototipação constituiu uma etapa fundamental dentro do desenvolvimento do sistema, pois permitiu alinhar o tema da pesquisa, a documentação levantada e a implementação das telas em uma mesma lógica de construção. Mais do que antecipar a aparência da aplicação, essa fase contribuiu para validar a coerência entre os objetivos do trabalho, os requisitos definidos e a forma como a solução seria efetivamente apresentada ao usuário final.

4\.3\.1. Descrição dos Protótipos

Com base nessa compreensão, apresentam-se a seguir as descrições das telas que compõem a prototipação do sistema. A opção por detalhar cada figura individualmente busca não apenas descrever a aparência das interfaces, mas explicitar a função que cada uma desempenha na solução proposta, sua relação com os requisitos identificados e sua contribuição para a organização da rotina clínica. A sequência numérica adotada, da Figura 04 à Figura 24, acompanha uma ordem lógica de apresentação, iniciando pelo acesso ao sistema e avançando pela visão geral da aplicação, pelos fluxos de cadastro, agendamento, acompanhamento profissional e administração.

Figura 04 - Tela de Login

Fonte: Autor (2026).

A tela de Login representa o ponto de entrada da aplicação e materializa, no nível da interface, o requisito de autenticação e controle de acesso por perfil. Sua organização simples, com campos de credenciais e acesso direto ao ambiente interno após validação, reforça a preocupação com segurança, restrição de funcionalidades por tipo de usuário e proteção das informações operacionais da clínica. Na prototipação, essa tela evidencia que o sistema foi concebido para iniciar sua rotina de uso a partir de um processo controlado de identificação do usuário, aspecto essencial para a confiabilidade da solução proposta.

Figura 05 - Página Inicial do Sistema

Fonte: Autor (2026).

A Página Inicial constitui o primeiro contato do usuário com o ambiente administrativo da aplicação. Nela, estão reunidos cards com indicadores da rotina clínica, como total de agendamentos, pendências, confirmações e quantidade de pacientes cadastrados, além de uma tabela com os próximos atendimentos previstos. Essa tela é relevante para a monografia porque evidencia a centralização das informações essenciais em um único painel, permitindo acompanhamento rápido do cenário operacional e acesso direto às áreas mais utilizadas do sistema.

Figura 06 - Tutorial do Sistema

Fonte: Autor (2026).

A tela de Tutorial do Sistema foi concebida para orientar o usuário quanto à função de cada módulo disponível no ambiente. Nela, os grupos funcionais são apresentados em blocos explicativos, acompanhados da descrição dos submódulos, do fluxo básico de uso e dos perfis que normalmente utilizam cada área. Sua presença é importante porque demonstra a preocupação em tornar a solução mais acessível, especialmente para novos usuários, reforçando a clareza da navegação e a compreensão das etapas do atendimento dentro do sistema.

Figura 07 - Minha Conta

Fonte: Autor (2026).

A tela Minha Conta corresponde ao espaço em que o próprio usuário pode consultar e atualizar suas informações pessoais e de acesso. Essa interface reúne dados do perfil autenticado e permite a manutenção de informações individuais sem interferir nas configurações administrativas mais amplas do sistema. Sua inserção no texto evidencia a preocupação com organização cadastral, autonomia do usuário e separação adequada entre dados pessoais e parâmetros institucionais da aplicação.

Figura 08 - Cadastro de Novo Paciente

Fonte: Autor (2026).

A tela de cadastro de paciente representa uma etapa fundamental do fluxo clínico, uma vez que o registro do atendido é condição necessária para a criação de agendamentos. A interface foi organizada em blocos de dados pessoais, contato e endereço, o que favorece a leitura, o preenchimento orientado e a consistência das informações inseridas. Sua presença na monografia mostra como o sistema estrutura a entrada inicial dos dados e busca reduzir falhas decorrentes de registros incompletos ou desorganizados.

Figura 09 - Listagem e Busca de Pacientes

Fonte: Autor (2026).

A tela de listagem e busca foi projetada para facilitar a localização de pacientes já cadastrados, permitindo consulta rápida por critérios como nome, CPF ou outras informações relevantes. Essa interface tem papel importante na rotina administrativa, pois evita duplicidades, agiliza o reaproveitamento de registros existentes e dá suporte às operações de visualização, edição e acompanhamento cadastral. Visualmente, ela evidencia a organização tabular dos dados e a disponibilização de ações rápidas diretamente na listagem.

Figura 10 - Logs de Pacientes

Fonte: Autor (2026).

A tela de logs de pacientes evidencia o registro histórico das alterações realizadas nos cadastros vinculados a esse módulo. Sua inclusão na descrição da prototipação é importante porque demonstra que o sistema não se limita ao armazenamento das informações, mas também oferece mecanismos de rastreabilidade sobre mudanças relevantes, indicando maior controle administrativo sobre os dados cadastrados. Desse modo, a interface reforça a confiabilidade do ambiente e a possibilidade de consulta posterior das alterações executadas.

Figura 11 - Calendário de Agendamentos

Fonte: Autor (2026).

A visualização em calendário foi desenvolvida para oferecer uma leitura gráfica e temporal dos compromissos registrados no sistema. Nessa tela, os atendimentos são distribuídos por período, facilitando a identificação de horários ocupados, dias com maior concentração de consultas e compromissos vinculados a profissionais específicos. Na monografia, sua apresentação ajuda a demonstrar a dimensão visual da agenda clínica e a complementaridade entre leitura cronológica e controle operacional.

Figura 12 - Agenda Geral

Fonte: Autor (2026).

A Agenda Geral reúne os agendamentos em formato tabular, com filtros por busca global, profissional e período, além de ações diretas de visualização, edição e cancelamento. Diferentemente do calendário, essa interface privilegia o detalhamento operacional, apresentando colunas como paciente, CPF, profissional, serviço, data, horário inicial, horário final e status do atendimento. Sua descrição é importante para evidenciar a visão gerencial do sistema e a forma como a solução organiza o acompanhamento minucioso da rotina clínica.

Figura 13 - Novo Agendamento

Fonte: Autor (2026).

A tela de novo agendamento representa o núcleo funcional da aplicação, pois é nela que se concretiza o processo principal da solução proposta. O formulário foi organizado em sequência lógica, relacionando paciente, profissional, procedimento, data e horário em um fluxo compatível com a rotina real de atendimento. Sua inserção na monografia é central, já que essa interface expressa diretamente as regras de negócio ligadas à disponibilidade, à organização da agenda e à consistência dos dados registrados antes da confirmação do compromisso.

Figura 14 - Confirmações

Fonte: Autor (2026).

A tela de confirmações foi pensada para agrupar os atendimentos que ainda dependem de retorno, atualização ou validação de status. Sua função é apoiar o controle diário dos compromissos, permitindo distinguir com clareza consultas pendentes, confirmadas ou canceladas a partir da situação apresentada no sistema. Na descrição acadêmica, essa interface evidencia a preocupação com o acompanhamento do ciclo do agendamento para além do simples cadastro, incorporando também o gerenciamento contínuo do estado operacional de cada atendimento.

Figura 15 - Agendamentos Finalizados

Fonte: Autor (2026).

A área de agendamentos finalizados concentra o histórico dos atendimentos já concluídos, preservando o registro das consultas encerradas no sistema. Essa tela é importante porque demonstra que a solução mantém memória operacional das atividades realizadas, permitindo consultas posteriores para fins administrativos, organizacionais e de acompanhamento interno. Sua representação na monografia reforça a continuidade do fluxo, desde o cadastro inicial até o encerramento efetivo do atendimento.

Figura 16 - Seu Calendário

Fonte: Autor (2026).

A tela Seu Calendário foi estruturada para disponibilizar ao profissional autenticado apenas os compromissos diretamente vinculados à sua agenda. Essa personalização da visualização reforça o controle de acesso por perfil e demonstra como o sistema adapta a experiência conforme a responsabilidade de cada usuário, limitando a exibição ao contexto de trabalho daquele profissional. No contexto da prototipação, a tela mostra que a solução não foi pensada apenas do ponto de vista administrativo, mas também da rotina prática do profissional de saúde.

Figura 17 - Fila de Espera

Fonte: Autor (2026).

A interface da fila de espera organiza os pacientes que aguardam atendimento, funcionando como apoio direto à dinâmica operacional da clínica. Sua função é tornar mais clara a ordem dos atendimentos em andamento e facilitar a percepção dos casos que demandam ação imediata, especialmente em cenários de maior fluxo. A apresentação dessa tela no texto acadêmico evidencia a preocupação em apoiar o profissional na condução da rotina, com foco em objetividade e acompanhamento contínuo dos pacientes aguardando atendimento.

Figura 18 - Atendimentos em Atraso

Fonte: Autor (2026).

A tela de atendimentos em atraso destaca situações em que o horário previsto para encerramento já foi ultrapassado, mas o compromisso ainda não recebeu definição no sistema. Trata-se de uma interface relevante para o acompanhamento de exceções operacionais, pois permite rápida identificação de pendências que exigem intervenção e reposicionamento do fluxo diário. Na monografia, essa figura ajuda a mostrar que a solução foi concebida para lidar não apenas com o fluxo ideal, mas também com situações reais de atraso e acompanhamento extraordinário.

Figura 19 - Horário da Clínica

Fonte: Autor (2026).

A tela de horário da clínica registra os parâmetros institucionais que delimitam o funcionamento da unidade, como abertura, encerramento e intervalos internos. Sua importância na descrição da prototipação está no fato de que esses dados servem de base para as validações do sistema, sobretudo no momento do agendamento e na definição das janelas disponíveis para atendimento. Assim, a interface evidencia que a solução considera regras estruturais da clínica como parte integrante do controle operacional.

Figura 20 - Profissionais de Saúde

Fonte: Autor (2026).

A área de profissionais de saúde foi organizada para concentrar o cadastro, a identificação e a manutenção das informações dos responsáveis pelos atendimentos. Nessa tela, observam-se dados profissionais, vínculos com usuários do sistema e elementos relacionados à organização da agenda de trabalho, como identificação do profissional e parâmetros de atuação. Sua descrição é relevante porque demonstra como o sistema estrutura a base profissional necessária para a execução adequada dos agendamentos e demais rotinas clínicas.

Figura 21 - Procedimentos

Fonte: Autor (2026).

A tela de procedimentos reúne os serviços disponibilizados pela clínica, com informações como denominação, duração e relação com os profissionais responsáveis. Essa interface é indispensável para a correta composição dos agendamentos, pois vincula o tipo de atendimento à lógica de tempo, execução e planejamento da consulta. Ao ser apresentada na monografia, evidencia a integração entre os cadastros base e o fluxo operacional da aplicação.

Figura 22 - Usuários e Permissões

Fonte: Autor (2026).

A interface de usuários e permissões demonstra como o sistema organiza o controle de acesso aos módulos e submódulos disponíveis. Nela, definem-se perfis, níveis de utilização e restrições compatíveis com a função desempenhada por cada ator da clínica, assegurando que cada usuário visualize apenas as áreas pertinentes ao seu contexto de atuação. Sua inserção na descrição dos protótipos é importante por reforçar o compromisso da solução com segurança, segmentação de responsabilidades e organização hierárquica do ambiente.

Figura 23 - Logs de Atividade

Fonte: Autor (2026).

A tela de logs de atividade concentra os registros das principais ações realizadas no sistema, permitindo acompanhar alterações administrativas e operacionais executadas pelos usuários. Essa interface tem valor significativo na documentação da solução, pois demonstra a existência de mecanismos de rastreabilidade, auditoria e controle histórico sobre o uso da aplicação em diferentes módulos. Sua apresentação fortalece a compreensão de que o sistema foi planejado com foco em segurança e transparência das ações.

Figura 24 - Avisos Operacionais

Fonte: Autor (2026).

A tela de avisos operacionais representa a camada de comunicação interna da aplicação, reunindo notificações e alertas relevantes para a rotina clínica. Nela, o usuário pode acompanhar lembretes de agendamentos, pendências e informações que exigem atenção imediata no contexto diário de uso do sistema. Sua inclusão encerra adequadamente a sequência de figuras, pois evidencia um recurso de apoio que complementa o funcionamento dos módulos centrais e contribui para a fluidez da operação diária.

Em conjunto, as figuras descritas nesta subseção constroem uma visão progressiva e coerente da solução desenvolvida, iniciando pela navegação geral do sistema, passando pelos fluxos de cadastro e agendamento, avançando pelas áreas específicas do profissional e concluindo com os módulos administrativos e de apoio. Essa organização favorece a apresentação visual da monografia e permite que cada tela seja compreendida não apenas como elemento gráfico, mas como parte efetiva da lógica funcional da aplicação e da rotina clínica apoiada pelo sistema.

4\.4. Considerações sobre o Desenvolvimento

Com base no desenvolvimento realizado, observa-se que o sistema foi projetado para integrar, em uma única plataforma, os principais processos envolvidos na rotina de agendamento clínico. A solução contempla desde os cadastros base até o acompanhamento operacional dos atendimentos, permitindo maior controle das informações, melhor organização da agenda e suporte às decisões cotidianas da clínica.

Além da implementação das telas e funcionalidades, o desenvolvimento envolveu a definição de regras de negócio, validações de integridade, organização por perfis de acesso, modelagem relacional dos dados e estruturação de uma arquitetura que favorece a manutenção evolutiva do sistema. Dessa forma, a solução desenvolvida apresenta-se como uma ferramenta funcional, organizada e coerente com os objetivos desta pesquisa.

O ambiente de desenvolvimento foi estruturado para execução local em XAMPP, permitindo a utilização do servidor Apache, do interpretador PHP e do MySQL durante a implementação e os testes. O gerenciamento de dependências do back-end foi realizado com Composer, enquanto os recursos do front-end foram organizados com NPM e Vite. Essa combinação permitiu rapidez no desenvolvimento, padronização das dependências e facilidade de manutenção do projeto.
