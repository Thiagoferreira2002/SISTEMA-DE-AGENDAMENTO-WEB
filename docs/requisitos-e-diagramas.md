# Requisitos e Diagramas do Sistema

## Requisitos Funcionais

1. O sistema deve autenticar usuários e controlar acesso por perfil.
2. O sistema deve permitir acesso administrativo para os perfis admin, recepcionista, profissional e gestor da clínica, conforme permissões.
3. O sistema deve permitir cadastrar, editar, listar e visualizar pacientes.
4. O sistema deve validar duplicidade de paciente por CPF, e-mail e telefone.
5. O sistema deve permitir cadastrar, editar e listar profissionais de saúde.
6. O sistema deve permitir configurar horários da clínica e horários individuais dos profissionais.
7. O sistema deve permitir cadastrar procedimentos e definir duração de atendimento.
8.  O sistema deve permitir criar agendamentos vinculando paciente, profissional, procedimento .
9.  O sistema deve validar disponibilidade de horário antes de concluir um agendamento.
10. O sistema deve exibir agendamentos em agenda geral e em calendário.
11. O sistema deve permitir confirmar, pendenciar, cancelar e concluir agendamentos.
12. O sistema deve permitir listar confirmações e fila de espera.
13. O sistema deve permitir promover um paciente da fila de espera para um agendamento.
14. O sistema deve permitir que profissionais visualizem sua própria agenda e fila de atendimento.
15. O sistema deve permitir registrar histórico de serviços finalizados.
16. O sistema deve permitir exibir notificações relacionadas a agendamentos.
17. O sistema deve permitir que o usuário edite sua conta, incluindo foto de perfil.
18. O sistema deve registrar logs de atividade para ações relevantes do sistema.
19. O sistema deve permitir gerenciar usuários e permissões de módulos.

## Requisitos Não Funcionais

1. O sistema deve possuir interface web responsiva para desktop e mobile.
2. O sistema deve manter controle de acesso baseado em autenticação e perfil de usuário.
3. O sistema deve preservar integridade dos dados de agendamento e evitar conflitos de horário.
4. O sistema deve registrar trilha de auditoria para ações administrativas importantes.
5. O sistema deve operar com banco de dados relacional MySQL.
6. O sistema deve suportar idioma principal em português do Brasil.
7. O sistema deve apresentar tempo de resposta adequado para consultas de agenda e calendário.
8. O sistema deve permitir manutenção modular de cadastros base, agendamentos, pacientes e profissionais.
9. O sistema deve ser compatível com ambiente local em XAMPP e aplicação Laravel.
10. O sistema deve garantir persistência segura de arquivos enviados, como imagens de perfil.
11. O sistema deve usar layout consistente e navegabilidade clara entre módulos.
12. O sistema deve ser extensível para evolução futura de prontuário, prescrições e laudos.

## Diagrama de Caso de Uso

```mermaid
flowchart LR
    Admin[Admin]
    Recep[Recepcionista]
    Prof[Profissional]
    Gestor[Gestor da Clinica]

    UC1((Gerenciar pacientes))
    UC2((Gerenciar profissionais))
    UC3((Gerenciar procedimentos))
    UC4((Gerenciar convenios, planos e precos))
    UC5((Criar agendamento))
    UC6((Visualizar calendario e agenda))
    UC7((Confirmar ou cancelar agendamento))
    UC8((Atender fila do profissional))
    UC9((Concluir atendimento))
    UC10((Gerenciar unidades e salas))
    UC11((Ver servicos finalizados))
    UC12((Gerenciar usuarios e permissoes))
    UC13((Editar propria conta))
    UC14((Consultar notificacoes))

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14

    Recep --> UC1
    Recep --> UC5
    Recep --> UC6
    Recep --> UC7
    Recep --> UC11
    Recep --> UC13
    Recep --> UC14

    Gestor --> UC1
    Gestor --> UC5
    Gestor --> UC6
    Gestor --> UC7
    Gestor --> UC11
    Gestor --> UC13
    Gestor --> UC14

    Prof --> UC6
    Prof --> UC8
    Prof --> UC9
    Prof --> UC11
    Prof --> UC13
    Prof --> UC14
```

## Diagrama Modelo Entidade-Relacionamento (MER)

```mermaid
erDiagram
    USER ||--o{ AGENDAMENTO : cria
    USER ||--o| PROFESSIONAL : possui
    USER ||--o{ ACTIVITY_LOG : registra
    PATIENT ||--o{ AGENDAMENTO : possui
    PROFESSIONAL ||--o{ AGENDAMENTO : atende
    PROFESSIONAL ||--o{ PROFESSIONAL_SCHEDULE : define
    PROFESSIONAL ||--o{ PROCEDURE : executa
    PROCEDURE ||--o{ PROCEDURE_PRICE : possui
    INSURANCE ||--o{ INSURANCE_PLAN : contem
    INSURANCE ||--o{ PROCEDURE_PRICE : referencia
    INSURANCE_PLAN ||--o{ PROCEDURE_PRICE : referencia
    UNIT ||--o{ ROOM : contem
    UNIT ||--o{ AGENDAMENTO : recebe
    ROOM ||--o{ AGENDAMENTO : aloca
    PROCEDURE ||--o{ AGENDAMENTO : referencia
    INSURANCE ||--o{ AGENDAMENTO : cobre
    INSURANCE_PLAN ||--o{ AGENDAMENTO : detalha

    USER {
        bigint id PK
        string nome
        string sobrenome
        string email
        string cpf
        string nivel
        string permissions
    }

    PATIENT {
        bigint id PK
        string nome
        string email
        string telefone
        string cpf
        date data_nascimento
    }

    PROFESSIONAL {
        bigint id PK
        bigint user_id FK
        string nome
        string cpf
        string registro_tipo
        string agenda_color
    }

    PROFESSIONAL_SCHEDULE {
        bigint id PK
        bigint professional_id FK
        int day_of_week
        time start_time
        time end_time
    }

    PROCEDURE {
        bigint id PK
        bigint professional_id FK
        string nome
        int duracao_minutos
        boolean ativo
    }

    PROCEDURE_PRICE {
        bigint id PK
        bigint procedure_id FK
        bigint insurance_id FK
        bigint insurance_plan_id FK
        decimal valor
    }

    INSURANCE {
        bigint id PK
        string nome
        string cnpj
        boolean requires_guide
    }

    INSURANCE_PLAN {
        bigint id PK
        bigint insurance_id FK
        string nome
        string codigo
    }

    UNIT {
        bigint id PK
        string nome
        string endereco
        string telefone
    }

    ROOM {
        bigint id PK
        bigint unit_id FK
        string nome
        boolean ativo
    }

    AGENDAMENTO {
        bigint id PK
        bigint user_id FK
        bigint patient_id FK
        bigint professional_id FK
        bigint procedure_id FK
        bigint unit_id FK
        bigint room_id FK
        bigint insurance_id FK
        bigint insurance_plan_id FK
        date data_agendamento
        string horario
        string status
    }

    ACTIVITY_LOG {
        bigint id PK
        bigint user_id FK
        string action
        string subject_type
        bigint subject_id
    }
```

## Diagrama de Atividade

```mermaid
flowchart TD
    A[Inicio] --> B[Selecionar paciente]
    B --> C{Paciente ja cadastrado?}
    C -- Nao --> D[Cadastrar paciente]
    C -- Sim --> E[Selecionar profissional]
    D --> E
    E --> F[Selecionar procedimento]
    F --> G[Selecionar data e horario]
    G --> H[Validar disponibilidade]
    H -- Indisponivel --> I[Informar conflito e escolher novo horario]
    I --> G
    H -- Disponivel --> J[Selecionar unidade e sala]
    J --> K[Informar convenio ou atendimento particular]
    K --> L[Salvar agendamento]
    L --> M[Definir status inicial]
    M --> N[Exibir em agenda e calendario]
    N --> O{Atendimento realizado?}
    O -- Nao --> P[Confirmar, pendenciar ou cancelar]
    P --> Q[Fim]
    O -- Sim --> R[Profissional atende paciente]
    R --> S[Concluir atendimento]
    S --> T[Registrar historico e log]
    T --> Q[Fim]
```
