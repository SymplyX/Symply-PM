---
name: Crash
about: Report a crash in PocketMine-MP (not plugins)
title: Server crashed
labels:
    - 'Status: Unconfirmed'

body:
    - type: markdown
      attributes:
          value: |
              <!-- Welcome! Please report the details of the crash below. -->
              <!-- Provide as much information as possible for better assistance. -->

    - type: textarea
      attributes:
          label: "Description"
          description: |
              Describe the crash incident in detail.
          validations:
              required: true

    - type: textarea
      attributes:
          label: "Steps to Reproduce"
          description: |
              Provide steps to reproduce the crash, if possible.
          validations:
              required: false

    - type: textarea
      attributes:
          label: "Server Environment"
          description: |
              Provide details about the server environment:
              - PocketMine-MP version
              - Operating system
              - Plugins (if relevant)
              - Any additional context
          validations:
              required: true

    - type: textarea
      attributes:
          label: "Crashdump File"
          description: |
              If available, provide the crashdump file for analysis.
          validations:
              required: false

    - type: textarea
      attributes:
          label: "Additional Information"
          description: |
              Add any additional information that might be relevant.
          validations:
              required: false
---
