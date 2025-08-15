# Loxo API Endpoints Coverage

Этот файл отслеживает покрытие API эндпоинтов Loxo в нашем пакете.

## Легенда

- ✅ **Полностью реализовано** - метод добавлен в сервис, протестирован
- 🚧 **Частично реализовано** - базовая реализация есть, может потребовать доработка
- ❌ **Не реализовано** - эндпоинт не добавлен в пакет
- 📝 **Планируется** - в планах на следующие версии

## Статистика покрытия

**Всего эндпоинтов:** 144  
**Реализовано:** 2 (1.4%)  
**В разработке:** 0 (0%)  
**Не реализовано:** 142 (98.6%)

---

## Activity & Address Types
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/activity_types` | GET | ✅ | Полностью реализовано |
| `/{agency_slug}/address_types` | GET | ✅ | Полностью реализовано |

## Bonus & Payment Types
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/bonus_payment_types` | GET | ❌ | |
| `/{agency_slug}/bonus_types` | GET | ❌ | |

## Companies
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/companies` | GET, POST | ❌ | |
| `/{agency_slug}/companies/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/companies/{id}/merge` | POST | ❌ | |
| `/{agency_slug}/companies/{company_id}/addresses` | GET, POST | ❌ | |
| `/{agency_slug}/companies/{company_id}/addresses/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/companies/{company_id}/documents` | GET, POST | ❌ | |
| `/{agency_slug}/companies/{company_id}/documents/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/companies/{company_id}/documents/{company_document_id}/download` | GET | ❌ | |
| `/{agency_slug}/companies/{company_id}/emails` | GET, POST | ❌ | |
| `/{agency_slug}/companies/{company_id}/emails/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/companies/{company_id}/people` | GET | ❌ | |
| `/{agency_slug}/companies/{company_id}/phones` | GET, POST | ❌ | |
| `/{agency_slug}/companies/{company_id}/phones/{id}` | GET, PUT, DELETE | ❌ | |

## Company Types & Statuses
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/company_global_statuses` | GET | ❌ | |
| `/{agency_slug}/company_types` | GET | ❌ | |

## Compensation & Types
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/compensation_types` | GET | ❌ | |
| `/{agency_slug}/equity_types` | GET | ❌ | |
| `/{agency_slug}/fee_types` | GET | ❌ | |

## Geography
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/countries` | GET | ❌ | |
| `/{agency_slug}/countries/{country_id}/states` | GET | ❌ | |
| `/{agency_slug}/countries/{country_id}/states/{state_id}/cities` | GET | ❌ | |
| `/{agency_slug}/currencies` | GET | ❌ | |

## Deals & Workflows
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/deal_workflows` | GET, POST | ❌ | |
| `/{agency_slug}/deal_workflows/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/deals` | GET, POST | ❌ | |
| `/{agency_slug}/deals/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/deals/{deal_id}/events` | GET, POST | ❌ | |

## Demographics & Diversity
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/disability_statuses` | GET | ❌ | |
| `/{agency_slug}/diversity_types` | GET | ❌ | |
| `/{agency_slug}/ethnicities` | GET | ❌ | |
| `/{agency_slug}/genders` | GET | ❌ | |
| `/{agency_slug}/pronouns` | GET | ❌ | |
| `/{agency_slug}/veteran_statuses` | GET | ❌ | |

## Dynamic Fields
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/dynamic_fields` | GET, POST | ❌ | |
| `/{agency_slug}/dynamic_fields/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/dynamic_fields/{dynamic_field_id}/hierarchies` | GET, POST | ❌ | |
| `/{agency_slug}/dynamic_fields/{dynamic_field_id}/hierarchies/{id}` | GET, PUT, DELETE | ❌ | |

## Education
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/education_types` | GET | ❌ | |

## Email & Communication
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/email_tracking` | GET, POST | ❌ | |
| `/{agency_slug}/email_types` | GET | ❌ | |
| `/{agency_slug}/phone_types` | GET | ❌ | |
| `/{agency_slug}/sms` | GET, POST | ❌ | |
| `/{agency_slug}/sms/{id}` | GET, PUT, DELETE | ❌ | |

## Forms & Templates
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/form_templates` | GET, POST | ❌ | |
| `/{agency_slug}/form_templates/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/forms` | GET, POST | ❌ | |
| `/{agency_slug}/forms/{id}` | GET, PUT, DELETE | ❌ | |

## Jobs & Positions
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/job_categories` | GET | ❌ | |
| `/{agency_slug}/job_contact_types` | GET | ❌ | |
| `/{agency_slug}/job_listing_config` | GET, PUT | ❌ | |
| `/{agency_slug}/job_owner_types` | GET | ❌ | |
| `/{agency_slug}/job_statuses` | GET | ❌ | |
| `/{agency_slug}/job_types` | GET | ❌ | |
| `/{agency_slug}/jobs` | GET, POST | ❌ | |
| `/{agency_slug}/jobs/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/jobs/{id}/merge` | POST | ❌ | |
| `/{agency_slug}/jobs/{job_id}/apply` | POST | ❌ | |
| `/{agency_slug}/jobs/{job_id}/candidates` | GET, POST | ❌ | |
| `/{agency_slug}/jobs/{job_id}/candidates/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/jobs/{job_id}/contacts` | GET, POST | ❌ | |
| `/{agency_slug}/jobs/{job_id}/contacts/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/jobs/{job_id}/documents` | GET, POST | ❌ | |
| `/{agency_slug}/jobs/{job_id}/documents/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/jobs/{job_id}/documents/{job_document_id}/download` | GET | ❌ | |

## People & Candidates
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/people` | GET, POST | ❌ | |
| `/{agency_slug}/people/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{id}/merge` | POST | ❌ | |
| `/{agency_slug}/people/{person_id}/documents` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/documents/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/documents/{person_document_id}/download` | GET | ❌ | |
| `/{agency_slug}/people/{person_id}/education_profiles` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/education_profiles/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/emails` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/emails/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/job_profiles` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/job_profiles/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/list_items` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/list_items/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/phones` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/phones/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/resumes` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/resumes/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/resumes/{resume_id}/download` | GET | ❌ | |
| `/{agency_slug}/people/{person_id}/share` | POST | ❌ | |
| `/{agency_slug}/people/{person_id}/sms_opt_ins` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/sms_opt_ins/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/{person_id}/social_profiles` | GET, POST | ❌ | |
| `/{agency_slug}/people/{person_id}/social_profiles/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/people/emails` | GET, POST | ❌ | |
| `/{agency_slug}/people/phones` | GET, POST | ❌ | |
| `/{agency_slug}/people/update_by_email` | PUT | ❌ | |

## Person Events & Lists
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/person_events` | GET, POST | ❌ | |
| `/{agency_slug}/person_events/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/person_events/{person_event_id}/documents` | GET, POST | ❌ | |
| `/{agency_slug}/person_events/{person_event_id}/documents/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/person_events/{person_event_id}/documents/{person_event_document_id}/download` | GET | ❌ | |
| `/{agency_slug}/person_global_statuses` | GET | ❌ | |
| `/{agency_slug}/person_lists` | GET, POST | ❌ | |
| `/{agency_slug}/person_share_field_types` | GET | ❌ | |
| `/{agency_slug}/person_types` | GET | ❌ | |

## Placements & Performance
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/placements` | GET, POST | ❌ | |
| `/{agency_slug}/placements/{id}` | GET, PUT, DELETE | ❌ | |

## Scheduling
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/schedule_items` | GET, POST | ❌ | |
| `/{agency_slug}/schedule_items/{id}` | GET, PUT, DELETE | ❌ | |

## Scorecards & Evaluation
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/scorecards` | GET, POST | ❌ | |
| `/{agency_slug}/scorecards/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/scorecards/scorecard_recommendation_types` | GET | ❌ | |
| `/{agency_slug}/scorecards/scorecard_templates` | GET, POST | ❌ | |
| `/{agency_slug}/scorecards/scorecard_templates/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/scorecards/scorecard_types` | GET | ❌ | |
| `/{agency_slug}/scorecards/scorecard_visibility_types` | GET | ❌ | |

## Miscellaneous
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/merges` | GET | ❌ | |
| `/{agency_slug}/question_types` | GET | ❌ | |
| `/{agency_slug}/seniority_levels` | GET | ❌ | |
| `/{agency_slug}/social_profile_types` | GET | ❌ | |
| `/{agency_slug}/source_types` | GET, POST | ❌ | |
| `/{agency_slug}/source_types/{id}` | GET, PUT, DELETE | ❌ | |

## System & Administration
| Endpoint | Methods | Status | Notes |
|----------|---------|--------|-------|
| `/{agency_slug}/users` | GET | ❌ | |
| `/{agency_slug}/webhooks` | GET, POST | ❌ | |
| `/{agency_slug}/webhooks/{id}` | GET, PUT, DELETE | ❌ | |
| `/{agency_slug}/workflow_stages` | GET | ❌ | |
| `/{agency_slug}/workflows` | GET, POST | ❌ | |

---

## Планы развития

### Версия 1.1.0 (Планируется)
- **Приоритет 1:** Companies API (основные операции)
- **Приоритет 2:** People/Candidates API (основные операции)
- **Приоритет 3:** Jobs API (основные операции)

### Версия 1.2.0 (Планируется)
- **Приоритет 1:** Deals & Workflows
- **Приоритет 2:** Dynamic Fields
- **Приоритет 3:** Geography (Countries, States, Cities)

### Версия 1.3.0+ (Долгосрочные планы)
- Scorecards & Evaluation
- Forms & Templates
- Advanced Communication features
- Administrative features

---

## Как добавить новый эндпоинт

1. Добавьте метод в `LoxoApiInterface`
2. Реализуйте метод в `LoxoApiService`
3. Добавьте тест в `LoxoApiServiceTest`
4. Обновите документацию в README.md
5. Обновите этот файл покрытия
6. Обновите CHANGELOG.md

---

*Последнее обновление: {{ date('Y-m-d H:i:s') }}*
