# Loxo API Endpoints Coverage

This file tracks the coverage of Loxo API endpoints in our package.

> 📚 **Official Resources:**
> - [Loxo Website](https://loxo.co) - Official Loxo platform
> - [Loxo API Documentation](https://loxo.readme.io/reference/loxo-api) - Official API reference
> 
> ⚠️ **Note:** This is an unofficial package. All endpoint information is based on the [official Loxo API documentation](https://loxo.readme.io/reference/loxo-api).

## Legend

- ✅ **Fully implemented** - method added to service, tested
- 🚧 **Partially implemented** - basic implementation exists, may need refinement
- ❌ **Not implemented** - endpoint not added to package
- 📝 **Planned** - planned for future versions

## Coverage Statistics

**Total endpoints:** 144  
**Implemented:** 7 (4.9%)  
**In development:** 0 (0%)  
**Not implemented:** 137 (95.1%)

---

## Activity & Address Types
| Endpoint                        | Methods | Status | Notes             |
| ------------------------------- | ------- | ------ | ----------------- |
| `/{agency_slug}/activity_types` | GET     | ✅      | Fully implemented |
| `/{agency_slug}/address_types`  | GET     | ✅      | Fully implemented |

## Bonus & Payment Types
| Endpoint                             | Methods | Status | Notes             |
| ------------------------------------ | ------- | ------ | ----------------- |
| `/{agency_slug}/bonus_payment_types` | GET     | ✅      | Fully implemented |
| `/{agency_slug}/bonus_types`         | GET     | ✅      | Fully implemented |

## Companies
| Endpoint                                                                         | Methods          | Status | Notes             |
| -------------------------------------------------------------------------------- | ---------------- | ------ | ----------------- |
| `/{agency_slug}/companies`                                                       | GET, POST        | ✅      | Fully implemented |
| `/{agency_slug}/companies/{id}`                                                  | GET, PUT, DELETE | ❌      |                   |
| `/{agency_slug}/companies/{id}/merge`                                            | POST             | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/addresses`                                | GET, POST        | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/addresses/{id}`                           | GET, PUT, DELETE | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/documents`                                | GET, POST        | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/documents/{id}`                           | GET, PUT, DELETE | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/documents/{company_document_id}/download` | GET              | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/emails`                                   | GET, POST        | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/emails/{id}`                              | GET, PUT, DELETE | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/people`                                   | GET              | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/phones`                                   | GET, POST        | ❌      |                   |
| `/{agency_slug}/companies/{company_id}/phones/{id}`                              | GET, PUT, DELETE | ❌      |                   |

## Company Types & Statuses
| Endpoint                                 | Methods | Status | Notes |
| ---------------------------------------- | ------- | ------ | ----- |
| `/{agency_slug}/company_global_statuses` | GET     | ❌      |       |
| `/{agency_slug}/company_types`           | GET     | ❌      |       |

## Compensation & Types
| Endpoint                            | Methods | Status | Notes |
| ----------------------------------- | ------- | ------ | ----- |
| `/{agency_slug}/compensation_types` | GET     | ❌      |       |
| `/{agency_slug}/equity_types`       | GET     | ❌      |       |
| `/{agency_slug}/fee_types`          | GET     | ❌      |       |

## Geography
| Endpoint                                                         | Methods | Status | Notes |
| ---------------------------------------------------------------- | ------- | ------ | ----- |
| `/{agency_slug}/countries`                                       | GET     | ❌      |       |
| `/{agency_slug}/countries/{country_id}/states`                   | GET     | ❌      |       |
| `/{agency_slug}/countries/{country_id}/states/{state_id}/cities` | GET     | ❌      |       |
| `/{agency_slug}/currencies`                                      | GET     | ❌      |       |

## Deals & Workflows
| Endpoint                                | Methods          | Status | Notes |
| --------------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/deal_workflows`         | GET, POST        | ❌      |       |
| `/{agency_slug}/deal_workflows/{id}`    | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/deals`                  | GET, POST        | ❌      |       |
| `/{agency_slug}/deals/{id}`             | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/deals/{deal_id}/events` | GET, POST        | ❌      |       |

## Demographics & Diversity
| Endpoint                             | Methods | Status | Notes |
| ------------------------------------ | ------- | ------ | ----- |
| `/{agency_slug}/disability_statuses` | GET     | ❌      |       |
| `/{agency_slug}/diversity_types`     | GET     | ❌      |       |
| `/{agency_slug}/ethnicities`         | GET     | ❌      |       |
| `/{agency_slug}/genders`             | GET     | ❌      |       |
| `/{agency_slug}/pronouns`            | GET     | ❌      |       |
| `/{agency_slug}/veteran_statuses`    | GET     | ❌      |       |

## Dynamic Fields
| Endpoint                                                            | Methods          | Status | Notes |
| ------------------------------------------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/dynamic_fields`                                     | GET, POST        | ❌      |       |
| `/{agency_slug}/dynamic_fields/{id}`                                | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/dynamic_fields/{dynamic_field_id}/hierarchies`      | GET, POST        | ❌      |       |
| `/{agency_slug}/dynamic_fields/{dynamic_field_id}/hierarchies/{id}` | GET, PUT, DELETE | ❌      |       |

## Education
| Endpoint                         | Methods | Status | Notes |
| -------------------------------- | ------- | ------ | ----- |
| `/{agency_slug}/education_types` | GET     | ❌      |       |

## Email & Communication
| Endpoint                        | Methods          | Status | Notes |
| ------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/email_tracking` | GET, POST        | ❌      |       |
| `/{agency_slug}/email_types`    | GET              | ❌      |       |
| `/{agency_slug}/phone_types`    | GET              | ❌      |       |
| `/{agency_slug}/sms`            | GET, POST        | ❌      |       |
| `/{agency_slug}/sms/{id}`       | GET, PUT, DELETE | ❌      |       |

## Forms & Templates
| Endpoint                             | Methods          | Status | Notes |
| ------------------------------------ | ---------------- | ------ | ----- |
| `/{agency_slug}/form_templates`      | GET, POST        | ❌      |       |
| `/{agency_slug}/form_templates/{id}` | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/forms`               | GET, POST        | ❌      |       |
| `/{agency_slug}/forms/{id}`          | GET, PUT, DELETE | ❌      |       |

## Jobs & Positions
| Endpoint                                                            | Methods          | Status | Notes           |
| ------------------------------------------------------------------- | ---------------- | ------ | --------------- |
| `/{agency_slug}/job_categories`                                     | GET              | ❌      |                 |
| `/{agency_slug}/job_contact_types`                                  | GET              | ❌      |                 |
| `/{agency_slug}/job_listing_config`                                 | GET, PUT         | ❌      |                 |
| `/{agency_slug}/job_owner_types`                                    | GET              | ❌      |                 |
| `/{agency_slug}/job_statuses`                                       | GET              | ❌      |                 |
| `/{agency_slug}/job_types`                                          | GET              | ❌      |                 |
| `/{agency_slug}/jobs`                                               | GET, POST        | ✅      | GET implemented |
| `/{agency_slug}/jobs/{id}`                                          | GET, PUT, DELETE | ❌      |                 |
| `/{agency_slug}/jobs/{id}/merge`                                    | POST             | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/apply`                                | POST             | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/candidates`                           | GET, POST        | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/candidates/{id}`                      | GET, PUT, DELETE | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/contacts`                             | GET, POST        | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/contacts/{id}`                        | GET, PUT, DELETE | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/documents`                            | GET, POST        | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/documents/{id}`                       | GET, PUT, DELETE | ❌      |                 |
| `/{agency_slug}/jobs/{job_id}/documents/{job_document_id}/download` | GET              | ❌      |                 |

## People & Candidates
| Endpoint                                                                    | Methods          | Status | Notes |
| --------------------------------------------------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/people`                                                     | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{id}`                                                | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{id}/merge`                                          | POST             | ❌      |       |
| `/{agency_slug}/people/{person_id}/documents`                               | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/documents/{id}`                          | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/documents/{person_document_id}/download` | GET              | ❌      |       |
| `/{agency_slug}/people/{person_id}/education_profiles`                      | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/education_profiles/{id}`                 | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/emails`                                  | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/emails/{id}`                             | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/job_profiles`                            | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/job_profiles/{id}`                       | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/list_items`                              | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/list_items/{id}`                         | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/phones`                                  | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/phones/{id}`                             | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/resumes`                                 | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/resumes/{id}`                            | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/resumes/{resume_id}/download`            | GET              | ❌      |       |
| `/{agency_slug}/people/{person_id}/share`                                   | POST             | ❌      |       |
| `/{agency_slug}/people/{person_id}/sms_opt_ins`                             | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/sms_opt_ins/{id}`                        | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/{person_id}/social_profiles`                         | GET, POST        | ❌      |       |
| `/{agency_slug}/people/{person_id}/social_profiles/{id}`                    | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/people/emails`                                              | GET, POST        | ❌      |       |
| `/{agency_slug}/people/phones`                                              | GET, POST        | ❌      |       |
| `/{agency_slug}/people/update_by_email`                                     | PUT              | ❌      |       |

## Person Events & Lists
| Endpoint                                                                                       | Methods          | Status | Notes |
| ---------------------------------------------------------------------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/person_events`                                                                 | GET, POST        | ❌      |       |
| `/{agency_slug}/person_events/{id}`                                                            | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/person_events/{person_event_id}/documents`                                     | GET, POST        | ❌      |       |
| `/{agency_slug}/person_events/{person_event_id}/documents/{id}`                                | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/person_events/{person_event_id}/documents/{person_event_document_id}/download` | GET              | ❌      |       |
| `/{agency_slug}/person_global_statuses`                                                        | GET              | ❌      |       |
| `/{agency_slug}/person_lists`                                                                  | GET, POST        | ❌      |       |
| `/{agency_slug}/person_share_field_types`                                                      | GET              | ❌      |       |
| `/{agency_slug}/person_types`                                                                  | GET              | ❌      |       |

## Placements & Performance
| Endpoint                         | Methods          | Status | Notes |
| -------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/placements`      | GET, POST        | ❌      |       |
| `/{agency_slug}/placements/{id}` | GET, PUT, DELETE | ❌      |       |

## Scheduling
| Endpoint                             | Methods          | Status | Notes |
| ------------------------------------ | ---------------- | ------ | ----- |
| `/{agency_slug}/schedule_items`      | GET, POST        | ❌      |       |
| `/{agency_slug}/schedule_items/{id}` | GET, PUT, DELETE | ❌      |       |

## Scorecards & Evaluation
| Endpoint                                                   | Methods          | Status | Notes |
| ---------------------------------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/scorecards`                                | GET, POST        | ❌      |       |
| `/{agency_slug}/scorecards/{id}`                           | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/scorecards/scorecard_recommendation_types` | GET              | ❌      |       |
| `/{agency_slug}/scorecards/scorecard_templates`            | GET, POST        | ❌      |       |
| `/{agency_slug}/scorecards/scorecard_templates/{id}`       | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/scorecards/scorecard_types`                | GET              | ❌      |       |
| `/{agency_slug}/scorecards/scorecard_visibility_types`     | GET              | ❌      |       |

## Miscellaneous
| Endpoint                              | Methods          | Status | Notes |
| ------------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/merges`               | GET              | ❌      |       |
| `/{agency_slug}/question_types`       | GET              | ❌      |       |
| `/{agency_slug}/seniority_levels`     | GET              | ❌      |       |
| `/{agency_slug}/social_profile_types` | GET              | ❌      |       |
| `/{agency_slug}/source_types`         | GET, POST        | ❌      |       |
| `/{agency_slug}/source_types/{id}`    | GET, PUT, DELETE | ❌      |       |

## System & Administration
| Endpoint                         | Methods          | Status | Notes |
| -------------------------------- | ---------------- | ------ | ----- |
| `/{agency_slug}/users`           | GET              | ❌      |       |
| `/{agency_slug}/webhooks`        | GET, POST        | ❌      |       |
| `/{agency_slug}/webhooks/{id}`   | GET, PUT, DELETE | ❌      |       |
| `/{agency_slug}/workflow_stages` | GET              | ❌      |       |
| `/{agency_slug}/workflows`       | GET, POST        | ❌      |       |

---

## Development Roadmap

### Version 1.1.0 (Planned)
- **Priority 1:** Companies API (core operations)
- **Priority 2:** People/Candidates API (core operations)
- **Priority 3:** Jobs API (core operations)

### Version 1.2.0 (Planned)
- **Priority 1:** Deals & Workflows
- **Priority 2:** Dynamic Fields
- **Priority 3:** Geography (Countries, States, Cities)

### Version 1.3.0+ (Long-term plans)
- Scorecards & Evaluation
- Forms & Templates
- Advanced Communication features
- Administrative features

---

## How to Add a New Endpoint

1. **Research** - Check the [official Loxo API documentation](https://loxo.readme.io/reference/loxo-api) for endpoint details
2. Add method to `LoxoApiInterface`
3. Implement method in `LoxoApiService`
4. Add test to `LoxoApiServiceTest`
5. Update documentation in README.md
6. Update this coverage file
7. Update CHANGELOG.md

> 📖 **Reference:** Always consult the [official API documentation](https://loxo.readme.io/reference/loxo-api) for accurate parameter lists and response formats.

---

*Last updated: 2024-12-19 - Companies endpoints (GET/POST) added*
