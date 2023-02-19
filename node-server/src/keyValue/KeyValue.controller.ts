import { Controller, Body, Get, Param, Post } from '@nestjs/common';
import { KeyValueService } from './keyValue.service';
import { KeyValueDto } from './types/keyValue.dto';
import { KeyValueResponseInterface } from './types/keyValueResponse.interface';

@Controller('key-value')
export class KeyValueController {
  constructor(private readonly keyValueService: KeyValueService) {}

  @Get(':key')
  async findRecord(
    @Param('key') key: string,
  ): Promise<KeyValueResponseInterface> {
    const keyValue = await this.keyValueService.findByKey(key);

    return this.keyValueService.buildResponse(keyValue);
  }

  @Post()
  async setValue(@Body() keyValueDto: KeyValueDto) {
    const keyValue = await this.keyValueService.setValue(
      keyValueDto.key,
      keyValueDto.value,
    );

    return this.keyValueService.buildResponse(keyValue);
  }
}
