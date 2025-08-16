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

## Coverage Statistics

**Total endpoints:** 144  
**Implemented:** 24 (16.7%)  
**In development:** 0 (0%)  
**Not implemented:** 120 (83.3%)

---

## Activity & Address Types
| Endpoint          | Methods | Status | Notes             |
| ----------------- | ------- | ------ | ----------------- |
| `/activity_types` | GET     | ✅      | Fully implemented |
| `/address_types`  | GET     | ✅      | Fully implemented |

## Bonus & Payment Types
| Endpoint               | Methods | Status | Notes             |
| ---------------------- | ------- | ------ | ----------------- |
| `/bonus_payment_types` | GET     | ✅      | Fully implemented |
| `/bonus_types`         | GET     | ✅      | Fully implemented |

## Companies
| Endpoint                                                           | Methods          | Status | Notes             |
| ------------------------------------------------------------------ | ---------------- | ------ | ----------------- |
| `/companies`                                                       | GET, POST        | ✅      | Fully implemented |
| `/companies/{id}`                                                  | GET, PUT, DELETE | ❌      |                   |
| `/companies/{id}/merge`                                            | POST             | ❌      |                   |
| `/companies/{company_id}/addresses`                                | GET, POST        | ❌      |                   |
| `/companies/{company_id}/addresses/{id}`                           | GET, PUT, DELETE | ❌      |                   |
| `/companies/{company_id}/documents`                                | GET, POST        | ❌      |                   |
| `/companies/{company_id}/documents/{id}`                           | GET, PUT, DELETE | ❌      |                   |
| `/companies/{company_id}/documents/{company_document_id}/download` | GET              | ❌      |                   |
| `/companies/{company_id}/emails`                                   | GET, POST        | ❌      |                   |
| `/companies/{company_id}/emails/{id}`                              | GET, PUT, DELETE | ❌      |                   |
| `/companies/{company_id}/people`                                   | GET              | ❌      |                   |
| `/companies/{company_id}/phones`                                   | GET, POST        | ❌      |                   |
| `/companies/{company_id}/phones/{id}`                              | GET, PUT, DELETE | ❌      |                   |

## Company Types & Statuses
| Endpoint                   | Methods | Status | Notes |
| -------------------------- | ------- | ------ | ----- |
| `/company_global_statuses` | GET     | ❌      |       |
| `/company_types`           | GET     | ❌      |       |

## Compensation & Types
| Endpoint              | Methods | Status | Notes |
| --------------------- | ------- | ------ | ----- |
| `/compensation_types` | GET     | ❌      |       |
| `/equity_types`       | GET     | ❌      |       |
| `/fee_types`          | GET     | ❌      |       |

## Geography
| Endpoint                                           | Methods | Status | Notes |
| -------------------------------------------------- | ------- | ------ | ----- |
| `/countries`                                       | GET     | ❌      |       |
| `/countries/{country_id}/states`                   | GET     | ❌      |       |
| `/countries/{country_id}/states/{state_id}/cities` | GET     | ❌      |       |
| `/currencies`                                      | GET     | ❌      |       |

## Deals & Workflows
| Endpoint                  | Methods          | Status | Notes |
| ------------------------- | ---------------- | ------ | ----- |
| `/deal_workflows`         | GET, POST        | ❌      |       |
| `/deal_workflows/{id}`    | GET, PUT, DELETE | ❌      |       |
| `/deals`                  | GET, POST        | ❌      |       |
| `/deals/{id}`             | GET, PUT, DELETE | ❌      |       |
| `/deals/{deal_id}/events` | GET, POST        | ❌      |       |

## Demographics & Diversity
| Endpoint               | Methods | Status | Notes             |
| ---------------------- | ------- | ------ | ----------------- |
| `/disability_statuses` | GET     | ❌      |                   |
| `/diversity_types`     | GET     | ❌      |                   |
| `/ethnicities`         | GET     | ❌      |                   |
| `/genders`             | GET     | ❌      |                   |
| `/pronouns`            | GET     | ❌      |                   |
| `/veteran_statuses`    | GET     | ✅      | Fully implemented |

## Dynamic Fields
| Endpoint                                              | Methods          | Status | Notes |
| ----------------------------------------------------- | ---------------- | ------ | ----- |
| `/dynamic_fields`                                     | GET, POST        | ❌      |       |
| `/dynamic_fields/{id}`                                | GET, PUT, DELETE | ❌      |       |
| `/dynamic_fields/{dynamic_field_id}/hierarchies`      | GET, POST        | ❌      |       |
| `/dynamic_fields/{dynamic_field_id}/hierarchies/{id}` | GET, PUT, DELETE | ❌      |       |

## Education
| Endpoint           | Methods | Status | Notes |
| ------------------ | ------- | ------ | ----- |
| `/education_types` | GET     | ❌      |       |

## Email & Communication
| Endpoint          | Methods          | Status | Notes |
| ----------------- | ---------------- | ------ | ----- |
| `/email_tracking` | GET, POST        | ❌      |       |
| `/email_types`    | GET              | ❌      |       |
| `/phone_types`    | GET              | ❌      |       |
| `/sms`            | GET, POST        | ❌      |       |
| `/sms/{id}`       | GET, PUT, DELETE | ❌      |       |

## Forms & Templates
| Endpoint               | Methods          | Status | Notes |
| ---------------------- | ---------------- | ------ | ----- |
| `/form_templates`      | GET, POST        | ❌      |       |
| `/form_templates/{id}` | GET, PUT, DELETE | ❌      |       |
| `/forms`               | GET, POST        | ❌      |       |
| `/forms/{id}`          | GET, PUT, DELETE | ❌      |       |

## Jobs & Positions
| Endpoint                                              | Methods          | Status | Notes             |
| ----------------------------------------------------- | ---------------- | ------ | ----------------- |
| `/job_categories`                                     | GET              | ❌      |                   |
| `/job_contact_types`                                  | GET              | ❌      |                   |
| `/job_listing_config`                                 | GET, PUT         | ❌      |                   |
| `/job_owner_types`                                    | GET              | ❌      |                   |
| `/job_statuses`                                       | GET              | ❌      |                   |
| `/job_types`                                          | GET              | ❌      |                   |
| `/jobs`                                               | GET, POST        | 🚧      | GET implemented   |
| `/jobs/{id}`                                          | GET, PUT, DELETE | ❌      |                   |
| `/jobs/{id}/merge`                                    | POST             | ❌      |                   |
| `/jobs/{job_id}/apply`                                | POST             | ❌      |                   |
| `/jobs/{job_id}/candidates`                           | GET              | ✅      |                   |
| `/jobs/{job_id}/candidates/{id}`                      | GET, PUT         | ✅      | Fully implemented |
| `/jobs/{job_id}/contacts`                             | GET, POST        | ❌      |                   |
| `/jobs/{job_id}/contacts/{id}`                        | GET, PUT, DELETE | ❌      |                   |
| `/jobs/{job_id}/documents`                            | GET, POST        | ❌      |                   |
| `/jobs/{job_id}/documents/{id}`                       | GET, PUT, DELETE | ❌      |                   |
| `/jobs/{job_id}/documents/{job_document_id}/download` | GET              | ❌      |                   |

## People & Candidates
| Endpoint                                                      | Methods          | Status | Notes             |
| ------------------------------------------------------------- | ---------------- | ------ | ----------------- |
| `/people`                                                     | GET, POST        | ✅      | Fully implemented |
| `/people/{id}`                                                | GET, PUT         | ✅      | Fully implemented |
| `/people/{id}/merge`                                          | POST             | ❌      |                   |
| `/people/{person_id}/documents`                               | GET, POST        | ❌      |                   |
| `/people/{person_id}/documents/{id}`                          | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/documents/{person_document_id}/download` | GET              | ❌      |                   |
| `/people/{person_id}/education_profiles`                      | GET, POST        | ❌      |                   |
| `/people/{person_id}/education_profiles/{id}`                 | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/emails`                                  | GET, POST        | ❌      |                   |
| `/people/{person_id}/emails/{id}`                             | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/job_profiles`                            | GET, POST        | ❌      |                   |
| `/people/{person_id}/job_profiles/{id}`                       | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/list_items`                              | GET, POST        | ❌      |                   |
| `/people/{person_id}/list_items/{id}`                         | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/phones`                                  | GET, POST        | ❌      |                   |
| `/people/{person_id}/phones/{id}`                             | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/resumes`                                 | GET, POST        | ❌      |                   |
| `/people/{person_id}/resumes/{id}`                            | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/resumes/{resume_id}/download`            | GET              | ❌      |                   |
| `/people/{person_id}/share`                                   | POST             | ❌      |                   |
| `/people/{person_id}/sms_opt_ins`                             | GET, POST        | ❌      |                   |
| `/people/{person_id}/sms_opt_ins/{id}`                        | GET, PUT, DELETE | ❌      |                   |
| `/people/{person_id}/social_profiles`                         | GET, POST        | ❌      |                   |
| `/people/{person_id}/social_profiles/{id}`                    | GET, PUT, DELETE | ❌      |                   |
| `/people/emails`                                              | GET, POST        | ❌      |                   |
| `/people/phones`                                              | GET, POST        | ❌      |                   |
| `/people/update_by_email`                                     | PUT              | ❌      |                   |

## Person Events & Lists
| Endpoint                                                                         | Methods          | Status | Notes             |
| -------------------------------------------------------------------------------- | ---------------- | ------ | ----------------- |
| `/person_events`                                                                 | GET, POST        | ✅      | Fully implemented |
| `/person_events/{id}`                                                            | GET, PUT, DELETE | ❌      |                   |
| `/person_events/{person_event_id}/documents`                                     | GET, POST        | ❌      |                   |
| `/person_events/{person_event_id}/documents/{id}`                                | GET, PUT, DELETE | ❌      |                   |
| `/person_events/{person_event_id}/documents/{person_event_document_id}/download` | GET              | ❌      |                   |
| `/person_global_statuses`                                                        | GET              | ❌      |                   |
| `/person_lists`                                                                  | GET, POST        | ❌      |                   |
| `/person_share_field_types`                                                      | GET              | ❌      |                   |
| `/person_types`                                                                  | GET              | ❌      |                   |

## Placements & Performance
| Endpoint           | Methods          | Status | Notes |
| ------------------ | ---------------- | ------ | ----- |
| `/placements`      | GET, POST        | ❌      |       |
| `/placements/{id}` | GET, PUT, DELETE | ❌      |       |

## Scheduling
| Endpoint               | Methods          | Status | Notes |
| ---------------------- | ---------------- | ------ | ----- |
| `/schedule_items`      | GET, POST        | ❌      |       |
| `/schedule_items/{id}` | GET, PUT, DELETE | ❌      |       |

## Scorecards & Evaluation
| Endpoint                                     | Methods          | Status | Notes |
| -------------------------------------------- | ---------------- | ------ | ----- |
| `/scorecards`                                | GET, POST        | ❌      |       |
| `/scorecards/{id}`                           | GET, PUT, DELETE | ❌      |       |
| `/scorecards/scorecard_recommendation_types` | GET              | ❌      |       |
| `/scorecards/scorecard_templates`            | GET, POST        | ❌      |       |
| `/scorecards/scorecard_templates/{id}`       | GET, PUT, DELETE | ❌      |       |
| `/scorecards/scorecard_types`                | GET              | ❌      |       |
| `/scorecards/scorecard_visibility_types`     | GET              | ❌      |       |

## Miscellaneous
| Endpoint                | Methods          | Status | Notes |
| ----------------------- | ---------------- | ------ | ----- |
| `/merges`               | GET              | ❌      |       |
| `/question_types`       | GET              | ❌      |       |
| `/seniority_levels`     | GET              | ❌      |       |
| `/social_profile_types` | GET              | ❌      |       |
| `/source_types`         | GET, POST        | ❌      |       |
| `/source_types/{id}`    | GET, PUT, DELETE | ❌      |       |

## System & Administration
| Endpoint           | Methods          | Status | Notes             |
| ------------------ | ---------------- | ------ | ----------------- |
| `/users`           | GET              | ✅      | Fully implemented |
| `/webhooks`        | GET, POST        | ✅      | Fully implemented |
| `/webhooks/{id}`   | GET, PUT, DELETE | ✅      | Fully implemented |
| `/workflow_stages` | GET              | ✅      | Fully implemented |
| `/workflows`       | GET, POST        | 🚧      | GET implemented   |

---

## How to Add a New Endpoint

For detailed instructions on implementing new API endpoints, please see [CONTRIBUTING.md](CONTRIBUTING.md).